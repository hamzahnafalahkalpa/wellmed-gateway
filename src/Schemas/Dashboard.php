<?php

namespace Projects\WellmedGateway\Schemas;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Projects\WellmedGateway\Contracts\Schemas\Dashboard as DashboardContract;

/**
 * Dashboard Schema - Read-only access to dashboard metrics from Elasticsearch
 *
 * This class only handles READING metrics. Writing is done by backbone's DashboardMetricsService.
 */
class Dashboard implements DashboardContract
{
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
     * Get dashboard metrics from Elasticsearch
     */
    public function getDashboardMetrics(array $params): array
    {
        if (!$this->client) {
            throw new \Exception('Elasticsearch is not enabled or available');
        }

        try {
            $response = $this->client->search([
                'index' => $this->getIndexName($params['search_type'] ?? 'daily'),
                'body' => $this->buildQuery($params)
            ]);

            $responseArray = $response->asArray();
            return $this->formatResponse($responseArray, $params);

        } catch (\Exception $e) {
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
            ['term' => ['period_type' => $params['search_type'] ?? 'daily']]
        ];

        if (!empty($params['search_tenant_id'])) {
            $must[] = ['term' => ['tenant_id' => $params['search_tenant_id']]];
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
        switch ($params['search_type'] ?? 'daily') {
            case 'daily':
                if (!empty($params['search_date'])) {
                    $must[] = ['term' => ['date' => Carbon::parse($params['search_date'])->format('Y-m-d')]];
                }
                break;
            case 'weekly':
                if (!empty($params['search_year'])) {
                    $must[] = ['term' => ['year' => (int) $params['search_year']]];
                }
                if (!empty($params['search_week'])) {
                    $must[] = ['term' => ['week' => (int) $params['search_week']]];
                }
                break;
            case 'monthly':
                if (!empty($params['search_year'])) {
                    $must[] = ['term' => ['year' => (int) $params['search_year']]];
                }
                if (!empty($params['search_month'])) {
                    $must[] = ['term' => ['month' => (int) $params['search_month']]];
                }
                break;
            case 'yearly':
                if (!empty($params['search_year'])) {
                    $must[] = ['term' => ['year' => (int) $params['search_year']]];
                }
                break;
        }
    }

    /**
     * Get Elasticsearch index name (matches backbone's naming)
     */
    protected function getIndexName(string $periodType): string
    {
        $prefix = config('elasticsearch.prefix', 'development');
        $separator = config('elasticsearch.separator', '.');
        return $prefix . $separator . $this->indexPrefix . '-' . $periodType;
    }

    /**
     * Format Elasticsearch response - returns data as-is from ES with fallback defaults
     */
    protected function formatResponse(array $response, array $params): array
    {
        $now = Carbon::now();
        $periodType = $params['search_type'] ?? 'daily';

        // No data found - return defaults
        if (empty($response['hits']['hits'])) {
            return $this->getDefaultResponse($periodType, $now);
        }

        $hit = $response['hits']['hits'][0]['_source'];

        // Return ES data with fallbacks for missing fields
        return [
            'motivational_stats' => $hit['motivational_stats'] ?? $this->getDefaultMotivationalStats(),
            'statistics' => $hit['statistics'] ?? [],
            'pending_items' => $hit['pending_items'] ?? [],
            'cashier' => $hit['cashier'] ?? [],
            'billing' => $hit['billing'] ?? [],
            'queue_services' => $hit['queue_services'] ?? [],
            'diagnosis_treatment' => $hit['diagnosis_treatment'] ?? [],
            'workspace_integrations' => $hit['workspace_integrations'] ?? [],
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
     * Get default response when no data exists
     */
    protected function getDefaultResponse(string $periodType, Carbon $now): array
    {
        return [
            'motivational_stats' => $this->getDefaultMotivationalStats(),
            'statistics' => [],
            'pending_items' => [],
            'cashier' => [],
            'billing' => [],
            'queue_services' => [],
            'diagnosis_treatment' => [],
            'workspace_integrations' => [],
            'trends' => $this->getDefaultTrends($periodType, $now),
            'meta' => [
                'period_type' => $periodType,
                'message' => 'No data available for the specified period',
                'timestamp' => $now->toIso8601String(),
                'date' => $now->format('Y-m-d'),
                'data_source' => 'elasticsearch',
            ],
        ];
    }

    protected function getDefaultMotivationalStats(): array
    {
        return [
            'current' => 0,
            'target' => 0,
            'percentage' => 0,
            'remaining' => 0,
            'growth' => 0,
            'growth_percentage' => 0,
        ];
    }

    protected function getDefaultTrends(string $periodType, Carbon $now): array
    {
        $labels = ['Kunjungan'];
        $counts = match ($periodType) {
            'daily' => 7,
            'weekly' => 4,
            'monthly' => 12,
            'yearly' => 5,
            default => 7
        };

        for ($i = $counts - 1; $i >= 0; $i--) {
            $labels[] = match ($periodType) {
                'daily' => $now->copy()->subDays($i)->format('d M'),
                'weekly' => 'W' . $now->copy()->subWeeks($i)->format('W'),
                'monthly' => $now->copy()->subMonths($i)->format('M Y'),
                'yearly' => $now->copy()->subYears($i)->format('Y'),
                default => $now->copy()->subDays($i)->format('d M')
            };
        }

        return [
            'services' => [],
            'dataset' => ['source' => [$labels]],
            'title' => 'Tren Kunjungan per Poliklinik',
            'subtitle' => match ($periodType) {
                'daily' => 'Berdasarkan 7 hari terakhir',
                'weekly' => 'Berdasarkan 4 minggu terakhir',
                'monthly' => 'Berdasarkan 12 bulan terakhir',
                'yearly' => 'Berdasarkan 5 tahun terakhir',
                default => ''
            },
            'chart_type' => 'line',
            'series_layout' => 'row',
            'period_type' => $periodType
        ];
    }
}
