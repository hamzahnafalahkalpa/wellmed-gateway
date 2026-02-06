<?php

namespace Projects\WellmedGateway\Schemas;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Hanafalah\ApiHelper\Concerns\HasDashboardMetricsDefaults;
use Projects\WellmedGateway\Contracts\Schemas\Dashboard as DashboardContract;

/**
 * Dashboard Schema - Read-only access to dashboard metrics from Elasticsearch
 *
 * This class only handles READING metrics. Writing is done by backbone's DashboardMetricsService.
 * When no data exists for the requested period, it creates a default document.
 */
class Dashboard implements DashboardContract
{
    use HasDashboardMetricsDefaults;

    protected $client;
    protected string $indexPrefix = 'dashboard-metrics';

    public function __construct()
    {
        if (config('elasticsearch.enabled', false)) {
            try {
                $this->client = app('elasticsearch');
            } catch (\Exception $e) {
                Log::warning('Elasticsearch client not available', ['error' => $e->getMessage()]);
                $this->client = null;
            }
        }
    }

    /**
     * Get dashboard metrics from Elasticsearch.
     * Creates default document if it doesn't exist.
     */
    public function getDashboardMetrics(array $params): array
    {
        if (!$this->client) {
            throw new \Exception('Elasticsearch is not enabled or available');
        }

        $periodType = $params['search_type'] ?? self::PERIOD_DAILY;
        $tenant_model = tenancy()->tenant;
        $tenantId = $params['search_tenant_id'] ?? $tenant_model?->getKey() ?? null;
        $workspaceId = $params['search_workspace_id'] ?? $tenant_model?->reference_id ?? null;

        if (!$tenantId || !$workspaceId) {
            throw new \Exception('Tenant ID and Workspace ID are required');
        }

        try {
            $this->ensureIndexExists($periodType);

            $response = $this->client->search([
                'index' => $this->getIndexName($periodType),
                'body' => $this->buildQuery($params)
            ]);

            $responseArray = $response->asArray();

            // If no data found, create default document
            if (empty($responseArray['hits']['hits'])) {
                $timestamp = $this->getTimestampFromParams($params);
                $this->createDefaultDocument($periodType, (int) $tenantId, $workspaceId, $timestamp);

                // Return the default response
                return $this->getDefaultDashboardResponse($periodType, $timestamp, $params);
            }

            return $this->formatDashboardResponse($responseArray, $params);

        } catch (\Exception $e) {
            // Check if index doesn't exist error
            if (str_contains($e->getMessage(), 'index_not_found_exception') ||
                str_contains($e->getMessage(), 'no such index')) {
                $this->ensureIndexExists($periodType);
                $timestamp = $this->getTimestampFromParams($params);
                $this->createDefaultDocument($periodType, (int) $tenantId, $workspaceId, $timestamp);

                return $this->getDefaultDashboardResponse($periodType, $timestamp, $params);
            }

            Log::error('Dashboard metrics query failed', [
                'error' => $e->getMessage(),
                'params' => $params
            ]);
            throw new \Exception('Failed to retrieve dashboard metrics: ' . $e->getMessage());
        }
    }

    /**
     * Build Elasticsearch query
     */
    protected function buildQuery(array $params): array
    {
        $must = [
            ['term' => ['period_type' => $params['search_type'] ?? self::PERIOD_DAILY]]
        ];

        if (!empty($params['search_tenant_id'])) {
            $must[] = ['term' => ['tenant_id' => (int) $params['search_tenant_id']]];
        }

        if (!empty($params['search_workspace_id'])) {
            $must[] = ['term' => ['workspace_id' => (int) $params['search_workspace_id']]];
        }

        // Date filtering based on search_type
        $this->addDateFilters($must, $params);

        return [
            'query' => ['bool' => ['must' => $must]],
            'sort' => [['timestamp' => ['order' => 'desc']]],
            'size' => 1
        ];
    }

    /**
     * Add date filters based on period type
     */
    protected function addDateFilters(array &$must, array $params): void
    {
        switch ($params['search_type'] ?? self::PERIOD_DAILY) {
            case self::PERIOD_DAILY:
                if (!empty($params['search_date'])) {
                    $must[] = ['term' => ['date' => Carbon::parse($params['search_date'])->format('Y-m-d')]];
                }
                break;
            case self::PERIOD_WEEKLY:
                if (!empty($params['search_year'])) {
                    $must[] = ['term' => ['year' => (int) $params['search_year']]];
                }
                if (!empty($params['search_week'])) {
                    $must[] = ['term' => ['week' => (int) $params['search_week']]];
                }
                break;
            case self::PERIOD_MONTHLY:
                if (!empty($params['search_year'])) {
                    $must[] = ['term' => ['year' => (int) $params['search_year']]];
                }
                if (!empty($params['search_month'])) {
                    $must[] = ['term' => ['month' => (int) $params['search_month']]];
                }
                break;
            case self::PERIOD_YEARLY:
                if (!empty($params['search_year'])) {
                    $must[] = ['term' => ['year' => (int) $params['search_year']]];
                }
                break;
        }
    }
}
