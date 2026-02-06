<?php

namespace Projects\WellmedGateway\Schemas;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Reporting Schema - Read/Query reporting data from Elasticsearch
 *
 * This class handles searching, filtering, and aggregating report data.
 */
class Reporting
{
    protected $client;

    // Report index types (must match backbone's ReportingService)
    public const INDEX_PATIENT = 'patient';
    public const INDEX_PATIENT_ILLNESS = 'patient_illness';
    public const INDEX_VISIT_PATIENT = 'visit_patient';
    public const INDEX_VISIT_REGISTRATION = 'visit_registration';
    public const INDEX_VISIT_EXAMINATION = 'visit_examination';
    public const INDEX_BILLING = 'billing';
    public const INDEX_INVOICE = 'invoice';
    public const INDEX_REFUND = 'refund';
    public const INDEX_WALLET_TRANSACTION = 'wallet_transaction';

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
     * Get index name with prefix.
     */
    public function getIndexName(string $indexType): string
    {
        $prefix = config('app.elasticsearch.index_prefix', config('app.env', 'development'));
        $separator = config('app.elasticsearch.index_separator', '.');
        $indexConfig = config("app.elasticsearch.indexes.{$indexType}", ['name' => $indexType]);

        return $prefix . $separator . ($indexConfig['name'] ?? $indexType);
    }

    /**
     * Search documents with pagination.
     */
    public function search(string $indexType, array $params = []): array
    {
        if (!$this->client) {
            throw new \Exception('Elasticsearch is not enabled or available');
        }

        try {
            $page = $params['page'] ?? 1;
            $perPage = $params['per_page'] ?? 10;
            $from = ($page - 1) * $perPage;

            $searchParams = [
                'index' => $this->getIndexName($indexType),
                'body' => [
                    'from' => $from,
                    'size' => $perPage,
                ],
            ];

            // Add query if filters provided
            if (!empty($params['filters'])) {
                $searchParams['body']['query'] = $this->buildQuery($params['filters']);
            }

            // Add sorting
            if (!empty($params['sort'])) {
                $searchParams['body']['sort'] = $this->buildSort($params['sort']);
            }

            $response = $this->client->search($searchParams);
            $responseArray = $response->asArray();

            return $this->formatPaginatedResponse($responseArray, $page, $perPage);

        } catch (\Exception $e) {
            Log::error("Reporting search failed for {$indexType}", [
                'error' => $e->getMessage(),
                'params' => $params,
            ]);
            throw new \Exception("Failed to search {$indexType}: " . $e->getMessage());
        }
    }

    /**
     * Build query from filters.
     */
    protected function buildQuery(array $filters): array
    {
        $must = [];

        foreach ($filters as $field => $value) {
            if (empty($value)) continue;

            // Wildcard search for text fields
            if (is_string($value) && str_contains($field, '.') || in_array($field, ['name', 'medical_record', 'disease_name'])) {
                $must[] = [
                    'query_string' => [
                        'query' => '*' . Str::lower($value) . '*',
                        'fields' => [$field],
                        'analyze_wildcard' => true,
                    ],
                ];
            } else {
                // Exact match for other fields
                $must[] = ['term' => [$field => $value]];
            }
        }

        return ['bool' => ['must' => $must]];
    }

    /**
     * Build sort from parameters.
     */
    protected function buildSort(array $sort): array
    {
        $result = [];
        foreach ($sort as $field => $order) {
            $result[] = [$field => ['order' => $order]];
        }
        return $result;
    }

    /**
     * Format response for pagination.
     */
    protected function formatPaginatedResponse(array $response, int $page, int $perPage): array
    {
        $hits = $response['hits'] ?? [];
        $total = $hits['total']['value'] ?? 0;
        $data = [];

        foreach ($hits['hits'] ?? [] as $hit) {
            $data[] = $hit['_source'] ?? [];
        }

        return [
            'data' => $data,
            'from' => ($page - 1) * $perPage + 1,
            'to' => min($page * $perPage, $total),
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => (int) ceil($total / $perPage),
        ];
    }

    /**
     * Get a single document by ID.
     */
    public function get(string $indexType, string $documentId): ?array
    {
        if (!$this->client) {
            return null;
        }

        try {
            $response = $this->client->get([
                'index' => $this->getIndexName($indexType),
                'id' => $documentId,
            ]);

            return $response['_source'] ?? null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Aggregate data by field.
     */
    public function aggregate(string $indexType, string $aggregationType, string $field, array $filters = []): array
    {
        if (!$this->client) {
            throw new \Exception('Elasticsearch is not enabled or available');
        }

        try {
            $body = [
                'size' => 0,
                'aggs' => [
                    'result' => [
                        $aggregationType => ['field' => $field],
                    ],
                ],
            ];

            if (!empty($filters)) {
                $body['query'] = $this->buildQuery($filters);
            }

            $response = $this->client->search([
                'index' => $this->getIndexName($indexType),
                'body' => $body,
            ]);

            $responseArray = $response->asArray();

            return [
                'value' => $responseArray['aggregations']['result']['value'] ?? 0,
                'total_documents' => $responseArray['hits']['total']['value'] ?? 0,
            ];
        } catch (\Exception $e) {
            Log::error("Aggregation failed for {$indexType}", [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Group by field (terms aggregation).
     */
    public function groupBy(string $indexType, string $field, array $filters = [], int $size = 100): array
    {
        if (!$this->client) {
            throw new \Exception('Elasticsearch is not enabled or available');
        }

        try {
            $body = [
                'size' => 0,
                'aggs' => [
                    'groups' => [
                        'terms' => [
                            'field' => $field,
                            'size' => $size,
                        ],
                    ],
                ],
            ];

            if (!empty($filters)) {
                $body['query'] = $this->buildQuery($filters);
            }

            $response = $this->client->search([
                'index' => $this->getIndexName($indexType),
                'body' => $body,
            ]);

            $responseArray = $response->asArray();
            $buckets = $responseArray['aggregations']['groups']['buckets'] ?? [];

            return array_map(fn($b) => [
                'key' => $b['key'],
                'count' => $b['doc_count'],
            ], $buckets);
        } catch (\Exception $e) {
            Log::error("Group by failed for {$indexType}", [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Count documents matching filters.
     */
    public function count(string $indexType, array $filters = []): int
    {
        if (!$this->client) {
            return 0;
        }

        try {
            $body = [];
            if (!empty($filters)) {
                $body['query'] = $this->buildQuery($filters);
            }

            $response = $this->client->count([
                'index' => $this->getIndexName($indexType),
                'body' => $body,
            ]);

            return $response['count'] ?? 0;
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /**
     * Date histogram aggregation for trends.
     */
    public function dateHistogram(string $indexType, string $dateField, string $interval = 'day', array $filters = []): array
    {
        if (!$this->client) {
            throw new \Exception('Elasticsearch is not enabled or available');
        }

        try {
            $body = [
                'size' => 0,
                'aggs' => [
                    'timeline' => [
                        'date_histogram' => [
                            'field' => $dateField,
                            'calendar_interval' => $interval,
                            'format' => 'yyyy-MM-dd',
                        ],
                    ],
                ],
            ];

            if (!empty($filters)) {
                $body['query'] = $this->buildQuery($filters);
            }

            $response = $this->client->search([
                'index' => $this->getIndexName($indexType),
                'body' => $body,
            ]);

            $responseArray = $response->asArray();
            $buckets = $responseArray['aggregations']['timeline']['buckets'] ?? [];

            return array_map(fn($b) => [
                'date' => $b['key_as_string'],
                'count' => $b['doc_count'],
            ], $buckets);
        } catch (\Exception $e) {
            Log::error("Date histogram failed for {$indexType}", [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    // ===== Convenience Methods for Specific Report Types =====

    /**
     * Search patients.
     */
    public function searchPatients(array $params = []): array
    {
        return $this->search(self::INDEX_PATIENT, $params);
    }

    /**
     * Search patient illness/diagnosis.
     */
    public function searchPatientIllness(array $params = []): array
    {
        return $this->search(self::INDEX_PATIENT_ILLNESS, $params);
    }

    /**
     * Search visit patients.
     */
    public function searchVisitPatients(array $params = []): array
    {
        return $this->search(self::INDEX_VISIT_PATIENT, $params);
    }

    /**
     * Search billings.
     */
    public function searchBillings(array $params = []): array
    {
        return $this->search(self::INDEX_BILLING, $params);
    }

    /**
     * Search invoices.
     */
    public function searchInvoices(array $params = []): array
    {
        return $this->search(self::INDEX_INVOICE, $params);
    }
}
