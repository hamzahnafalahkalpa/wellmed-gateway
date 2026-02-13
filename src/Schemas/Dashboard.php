<?php

namespace Projects\WellmedGateway\Schemas;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Projects\WellmedBackbone\Services\Concerns\HasDashboardMetricsDefaults;
use Projects\WellmedBackbone\Transformers\Dashboard\BillingTransformer;
use Projects\WellmedBackbone\Transformers\Dashboard\CashierTransformer;
use Projects\WellmedBackbone\Transformers\Dashboard\PendingItemTransformer;
use Projects\WellmedBackbone\Transformers\Dashboard\StatisticTransformer;
use Projects\WellmedBackbone\Transformers\Dashboard\WorkspaceIntegrationTransformer;
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

    protected StatisticTransformer $statisticTransformer;
    protected PendingItemTransformer $pendingItemTransformer;
    protected CashierTransformer $cashierTransformer;
    protected BillingTransformer $billingTransformer;
    protected WorkspaceIntegrationTransformer $workspaceIntegrationTransformer;

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

        // Initialize transformers
        $this->statisticTransformer = new StatisticTransformer();
        $this->pendingItemTransformer = new PendingItemTransformer();
        $this->cashierTransformer = new CashierTransformer();
        $this->billingTransformer = new BillingTransformer();
        $this->workspaceIntegrationTransformer = new WorkspaceIntegrationTransformer();
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
            $must[] = ['term' => ['workspace_id' => $params['search_workspace_id']]];
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

    /**
     * Override to apply transformers to the dashboard response.
     *
     * Transforms ES-only data to full frontend format with presentation data.
     */
    protected function formatDashboardResponse(array $response, array $params): array
    {
        $now = Carbon::now();
        $periodType = $params['search_type'] ?? self::PERIOD_DAILY;

        // No data found - return defaults with transformers applied
        if (empty($response['hits']['hits'])) {
            return $this->getDefaultDashboardResponse($periodType, $now, $params);
        }

        $hit = $response['hits']['hits'][0]['_source'];

        // Get raw data with fallbacks
        $statistics = $hit['statistics'] ?? $this->getDefaultStatistics($periodType);
        $pendingItems = $hit['pending_items'] ?? $this->getDefaultPendingItems($periodType);
        $cashier = $hit['cashier'] ?? $this->getDefaultCashier($periodType);
        $billing = $hit['billing'] ?? $this->getDefaultBilling($periodType);
        $workspaceIntegrations = $hit['workspace_integrations'] ?? $this->getDefaultWorkspaceIntegrations($periodType);

        // Merge motivational_stats with defaults to ensure all fields exist (including 'current')
        $motivationalStats = array_merge(
            $this->getDefaultMotivationalStats(),
            $hit['motivational_stats'] ?? []
        );

        // Apply transformers for presentation data
        return [
            'motivational_stats' => $motivationalStats,
            'statistics' => $this->statisticTransformer->transform($statistics, $periodType),
            'pending_items' => $this->pendingItemTransformer->transform($pendingItems, $periodType),
            'cashier' => $this->cashierTransformer->transform($cashier, $periodType),
            'billing' => $this->billingTransformer->transform($billing, $periodType),
            'queue_services' => [
                'current' => $hit['queue_services'] ?? [],
                'previous' => [],
            ],
            'diagnosis_treatment' => [
                'current' => $hit['diagnosis_treatment'] ?? [],
                'previous' => [],
            ],
            'workspace_integrations' => $this->workspaceIntegrationTransformer->transform($workspaceIntegrations, $periodType),
            'trends' => $hit['trends'] ?? $this->getDefaultTrends($periodType, $now),
            'meta' => [
                'period_type' => $hit['period_type'] ?? $periodType,
                'timestamp' => $hit['timestamp'] ?? $now->toIso8601String(),
                'date' => $hit['date'] ?? $now->format('Y-m-d'),
                'year' => $hit['year'] ?? $now->year,
                'month' => $hit['month'] ?? $now->month,
                'week' => $hit['week'] ?? (int) $now->format('W'),
                'day' => $hit['day'] ?? $now->day,
                'data_source' => 'elasticsearch',
                'aggregation_period' => $hit['aggregation_period'] ?? null,
            ],
        ];
    }

    /**
     * Override to apply transformers to default dashboard response.
     * Fetches previous period data for intelligent defaults.
     */
    protected function getDefaultDashboardResponse(string $periodType, Carbon $now, array $params = []): array
    {
        // Get tenant context
        $tenant_model = tenancy()->tenant;
        $tenantId = $params['search_tenant_id'] ?? $tenant_model?->getKey() ?? null;
        $workspaceId = $params['search_workspace_id'] ?? $tenant_model?->reference_id ?? null;

        // Fetch previous period data for intelligent defaults
        $previousData = null;
        if ($tenantId && $workspaceId) {
            $previousData = $this->fetchPreviousPeriodData($periodType, (int) $tenantId, $workspaceId, $now);
        }

        // Get data with previous period comparison
        if ($previousData) {
            $statistics = $this->getStatisticsWithPreviousData(
                $this->getDefaultStatistics($periodType),
                $previousData['statistics'] ?? [],
                $periodType
            );
            $motivationalStats = $this->getMotivationalStatsWithPreviousData(
                $this->getDefaultMotivationalStats(),
                $previousData['motivational_stats'] ?? [],
                $previousData['statistics'] ?? []
            );
            $pendingItems = $this->getPendingItemsWithPreviousData(
                $this->getDefaultPendingItems($periodType),
                $previousData['pending_items'] ?? [],
                $periodType
            );
            $cashier = $this->getCashierWithPreviousData(
                $this->getDefaultCashier($periodType),
                $previousData['cashier'] ?? [],
                $periodType
            );
            $billing = $this->getBillingWithPreviousData(
                $this->getDefaultBilling($periodType),
                $previousData['billing'] ?? [],
                $periodType
            );
            $queueServices = $this->getQueueServicesWithPreviousData(
                [],
                $previousData['queue_services'] ?? [],
                $periodType
            );
            $diagnosisTreatment = $this->getDiagnosisTreatmentWithPreviousData(
                [],
                $previousData['diagnosis_treatment'] ?? [],
                $periodType
            );
            $workspaceIntegrations = $this->getWorkspaceIntegrationsWithPreviousData(
                $this->getDefaultWorkspaceIntegrations($periodType),
                $previousData['workspace_integrations'] ?? [],
                $periodType
            );
            $trends = $this->getTrendsWithPreviousData(
                $this->getDefaultTrends($periodType, $now),
                $previousData['trends'] ?? [],
                $periodType,
                $now
            );
        } else {
            $statistics = $this->getDefaultStatistics($periodType);
            $motivationalStats = $this->getDefaultMotivationalStats();
            $pendingItems = $this->getDefaultPendingItems($periodType);
            $cashier = $this->getDefaultCashier($periodType);
            $billing = $this->getDefaultBilling($periodType);
            $queueServices = [
                'current' => [],
                'previous' => [],
            ];
            $diagnosisTreatment = [
                'current' => [],
                'previous' => [],
            ];
            $workspaceIntegrations = $this->getDefaultWorkspaceIntegrations($periodType);
            $trends = $this->getDefaultTrends($periodType, $now);
        }

        // Apply transformers for presentation data
        return [
            'motivational_stats' => $motivationalStats,
            'statistics' => $this->statisticTransformer->transform($statistics, $periodType),
            'pending_items' => $this->pendingItemTransformer->transform($pendingItems, $periodType),
            'cashier' => $this->cashierTransformer->transform($cashier, $periodType),
            'billing' => $this->billingTransformer->transform($billing, $periodType),
            'queue_services' => $queueServices,
            'diagnosis_treatment' => $diagnosisTreatment,
            'workspace_integrations' => $this->workspaceIntegrationTransformer->transform($workspaceIntegrations, $periodType),
            'trends' => $trends,
            'meta' => [
                'period_type' => $periodType,
                'timestamp' => $now->toIso8601String(),
                'date' => $now->format('Y-m-d'),
                'year' => $now->year,
                'month' => $now->month,
                'week' => (int) $now->format('W'),
                'day' => $now->day,
                'data_source' => 'elasticsearch',
                'aggregation_period' => null,
            ],
        ];
    }
}
