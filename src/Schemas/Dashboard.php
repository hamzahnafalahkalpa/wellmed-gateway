<?php

namespace Projects\WellmedGateway\Schemas;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Projects\WellmedGateway\Contracts\Schemas\Dashboard as DashboardContract;
use Hanafalah\LaravelSupport\Response;

class Dashboard implements DashboardContract
{
    protected $client;
    protected string $indexPattern = 'dashboard-metrics-*';

    public function __construct()
    {
        // Initialize Elasticsearch client if enabled
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
     * Get dashboard metrics from Elasticsearch or dummy data
     *
     * @param array $params
     * @return array
     */
    public function getDashboardMetrics(array $params)
    {
        // Check if dummy data is enabled
        if (env('DASHBOARD_USE_DUMMY_DATA', false)) {
            Log::info('Using dummy dashboard data', ['params' => $params]);
            return $this->getDummyDashboardData($params);
        }

        // Check if Elasticsearch is enabled and available
        if (!$this->client) {
            throw new \Exception('Elasticsearch is not enabled or available');
        }

        try {
            // Build Elasticsearch query based on search_type
            $query = $this->buildQuery($params);

            // Execute search
            $response = $this->client->search([
                'index' => $this->getIndexName($params),
                'body' => $query
            ]);

            // Convert Elasticsearch response object to array
            $responseArray = $response->asArray();

            // Format response
            $data = $this->formatResponse($responseArray, $params);
            return $data;

        } catch (\Exception $e) {
            Log::error('Dashboard metrics query failed', [
                'error' => $e->getMessage(),
                'params' => $params
            ]);

            throw new \Exception('Failed to retrieve dashboard metrics: ' . $e->getMessage());
        }
    }

    /**
     * Build Elasticsearch query based on search type
     *
     * @param array $params
     * @return array
     */
    protected function buildQuery(array $params): array
    {
        $must = [];

        // Filter by period_type
        $must[] = [
            'term' => [
                'period_type' => $params['search_type']
            ]
        ];

        // Filter by tenant_id if provided
        if (!empty($params['search_tenant_id'])) {
            $must[] = [
                'term' => [
                    'tenant_id' => $params['search_tenant_id']
                ]
            ];
        }

        // Filter by workspace_id if provided
        if (!empty($params['search_workspace_id'])) {
            $must[] = [
                'term' => [
                    'workspace_id' => $params['search_workspace_id']
                ]
            ];
        }

        // Add date filtering based on search_type
        switch ($params['search_type']) {
            case 'daily':
                if (!empty($params['search_date'])) {
                    $date = Carbon::parse($params['search_date']);
                    $must[] = [
                        'term' => [
                            'date' => $date->format('Y-m-d')
                        ]
                    ];
                }
                break;

            case 'monthly':
                if (!empty($params['search_year']) && !empty($params['search_month'])) {
                    $must[] = [
                        'term' => [
                            'year' => (int) $params['search_year']
                        ]
                    ];
                    $must[] = [
                        'term' => [
                            'month' => (int) $params['search_month']
                        ]
                    ];
                }
                break;

            case 'yearly':
                if (!empty($params['search_year'])) {
                    $must[] = [
                        'term' => [
                            'year' => (int) $params['search_year']
                        ]
                    ];
                }
                break;
        }

        return [
            'query' => [
                'bool' => [
                    'must' => $must
                ]
            ],
            'sort' => [
                ['timestamp' => ['order' => 'desc']]
            ],
            'size' => 1 // Get most recent document matching criteria
        ];
    }

    /**
     * Get Elasticsearch index name
     *
     * @param array $params
     * @return string
     */
    protected function getIndexName(array $params): string
    {
        // Use tenant-specific index if tenant_id provided
        if (!empty($params['search_tenant_id'])) {
            $prefix = config('elasticsearch.prefix', '');
            $separator = config('elasticsearch.separator', '.');

            if ($prefix) {
                return $prefix . $separator . $this->indexPattern;
            }
        }

        return $this->indexPattern;
    }

    /**
     * Format Elasticsearch response to match FE widget structure
     *
     * @param array $response
     * @param array $params
     * @return array
     */
    protected function formatResponse(array $response, array $params): array
    {
        $now = Carbon::now();

        if (empty($response['hits']['hits'])) {
            return [
                'motivational_stats' => [
                    'today' => 0,
                    'yesterday' => 0,
                    'target' => 0,
                    'percentage' => 0,
                ],
                'statistics' => [
                    $this->formatStatistic('patients', 'Jumlah Pasien', 0, 0, 'mdi:account-group', 'blue'),
                    $this->formatStatistic('new-patients', 'Pasien Baru', 0, 0, 'mdi:account-plus', 'purple'),
                    $this->formatStatistic('revenue', 'Omzet', 0, 0, 'mdi:cash-multiple', 'emerald', true),
                    $this->formatStatistic('unfinished', 'Tindakan Dipesankan', 0, 0, 'mdi:clipboard-list', 'orange'),
                ],
                'pending_items' => [],
                'queue_services' => [],
                'diagnosis_treatment' => [],
                'meta' => [
                    'period_type' => $params['search_type'],
                    'message' => 'No data available for the specified period',
                    'timestamp' => $now->toIso8601String(),
                    'date' => $now->format('Y-m-d'),
                    'data_source' => 'elasticsearch',
                ],
            ];
        }

        $hit = $response['hits']['hits'][0]['_source'];

        // Transform raw Elasticsearch data to FE widget structure
        return [
            'motivational_stats' => $hit['motivational_stats'] ?? [
                'today' => 0,
                'yesterday' => 0,
                'target' => 0,
                'percentage' => 0,
            ],
            'statistics' => $hit['statistics'] ?? [],
            'pending_items' => $hit['pending_items'] ?? [],
            'queue_services' => $hit['queue_services'] ?? [],
            'diagnosis_treatment' => $hit['diagnosis_treatment'] ?? [],
            'meta' => [
                'period_type' => $hit['period_type'] ?? $params['search_type'],
                'timestamp' => $hit['timestamp'] ?? $now->toIso8601String(),
                'date' => $hit['date'] ?? $now->format('Y-m-d'),
                'year' => $hit['year'] ?? $now->year,
                'month' => $hit['month'] ?? $now->month,
                'day' => $hit['day'] ?? $now->day,
                'data_source' => 'elasticsearch',
                'aggregation_period' => $hit['aggregation_period'] ?? null,
            ],
        ];
    }

    /**
     * Format a single statistic card for FE
     *
     * @param string $id
     * @param string $label
     * @param int $count
     * @param int $change
     * @param string $icon
     * @param string $color
     * @param bool $isCurrency
     * @return array
     */
    protected function formatStatistic(
        string $id,
        string $label,
        int $count,
        int $change,
        string $icon,
        string $color,
        bool $isCurrency = false
    ): array {
        $changeType = $change >= 0 ? 'increase' : 'decrease';
        $percentageChange = $count > 0 ? round(($change / ($count - $change)) * 100, 1) : 0;

        $colorMap = [
            'blue' => [
                'gradient' => 'from-blue-500 to-cyan-400',
                'bg_light' => 'bg-blue-50',
                'text_color' => 'text-blue-600',
                'border_color' => 'border-blue-200',
            ],
            'purple' => [
                'gradient' => 'from-purple-500 to-pink-400',
                'bg_light' => 'bg-purple-50',
                'text_color' => 'text-purple-600',
                'border_color' => 'border-purple-200',
            ],
            'emerald' => [
                'gradient' => 'from-emerald-500 to-teal-400',
                'bg_light' => 'bg-emerald-50',
                'text_color' => 'text-emerald-600',
                'border_color' => 'border-emerald-200',
            ],
            'orange' => [
                'gradient' => 'from-orange-500 to-amber-400',
                'bg_light' => 'bg-orange-50',
                'text_color' => 'text-orange-600',
                'border_color' => 'border-orange-200',
            ],
        ];

        $colors = $colorMap[$color] ?? $colorMap['blue'];

        $stat = [
            'id' => $id,
            'label' => $label,
            'count' => $count,
            'change' => abs($change),
            'change_type' => $changeType,
            'percentage_change' => abs($percentageChange),
            'change_label' => 'Dari kemarin',
            'icon' => $icon,
            'color' => $color,
            'gradient' => $colors['gradient'],
            'bg_light' => $colors['bg_light'],
            'text_color' => $colors['text_color'],
            'border_color' => $colors['border_color'],
        ];

        if ($isCurrency) {
            $stat['is_currency'] = true;
        }

        return $stat;
    }


    /**
     * Index dashboard metrics to Elasticsearch
     *
     * This method aggregates and indexes dashboard data in FE-friendly structure
     *
     * @param array $data Dashboard data with FE structure
     * @param string $periodType 'daily', 'monthly', or 'yearly'
     * @param array $metadata Additional metadata (tenant_id, workspace_id, etc.)
     * @return bool
     */
    public function indexDashboardMetrics(array $data, string $periodType, array $metadata = []): bool
    {
        if (!$this->client) {
            Log::warning('Cannot index dashboard metrics: Elasticsearch client not available');
            return false;
        }

        try {
            $now = Carbon::now();

            // Prepare document with FE-friendly structure
            $document = [
                'motivational_stats' => $data['motivational_stats'] ?? [
                    'today' => 0,
                    'yesterday' => 0,
                    'target' => 0,
                    'percentage' => 0,
                ],
                'statistics' => $data['statistics'] ?? [],
                'pending_items' => $data['pending_items'] ?? [],
                'queue_services' => $data['queue_services'] ?? [],
                'diagnosis_treatment' => $data['diagnosis_treatment'] ?? [],

                // Metadata for querying
                'period_type' => $periodType,
                'timestamp' => $now->toIso8601String(),
                'date' => $now->format('Y-m-d'),
                'year' => $now->year,
                'month' => $now->month,
                'day' => $now->day,
                'aggregation_period' => $periodType,

                // Optional tenant/workspace context
                'tenant_id' => $metadata['tenant_id'] ?? null,
                'workspace_id' => $metadata['workspace_id'] ?? null,
            ];

            // Generate document ID based on period and context
            $docId = $this->generateDocumentId($periodType, $now, $metadata);

            // Get index name
            $indexName = $this->getIndexNameForIndexing($metadata);

            // Index document (will update if exists)
            $response = $this->client->index([
                'index' => $indexName,
                'id' => $docId,
                'body' => $document,
                'refresh' => true, // Make immediately searchable
            ]);

            Log::info('Dashboard metrics indexed to Elasticsearch', [
                'index' => $indexName,
                'doc_id' => $docId,
                'period_type' => $periodType,
                'result' => $response['result'] ?? 'unknown',
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to index dashboard metrics to Elasticsearch', [
                'error' => $e->getMessage(),
                'period_type' => $periodType,
                'trace' => $e->getTraceAsString(),
            ]);

            return false;
        }
    }

    /**
     * Generate unique document ID for Elasticsearch
     *
     * @param string $periodType
     * @param Carbon $date
     * @param array $metadata
     * @return string
     */
    protected function generateDocumentId(string $periodType, Carbon $date, array $metadata): string
    {
        $parts = [$periodType];

        switch ($periodType) {
            case 'daily':
                $parts[] = $date->format('Y-m-d');
                break;
            case 'monthly':
                $parts[] = $date->format('Y-m');
                break;
            case 'yearly':
                $parts[] = $date->format('Y');
                break;
        }

        if (!empty($metadata['tenant_id'])) {
            $parts[] = 'tenant-' . $metadata['tenant_id'];
        }

        if (!empty($metadata['workspace_id'])) {
            $parts[] = 'workspace-' . $metadata['workspace_id'];
        }

        return implode('-', $parts);
    }

    /**
     * Get index name for indexing (writing)
     *
     * @param array $metadata
     * @return string
     */
    protected function getIndexNameForIndexing(array $metadata): string
    {
        $prefix = config('elasticsearch.prefix', '');
        $separator = config('elasticsearch.separator', '.');

        if (!empty($metadata['tenant_id']) && $prefix) {
            return $prefix . $separator . 'dashboard-metrics';
        }

        return 'dashboard-metrics';
    }

    /**
     * Generate dummy dashboard data for frontend testing
     * Structured to match FE widget requirements exactly
     *
     * @param array $params
     * @return array
     */
    protected function getDummyDashboardData(array $params): array
    {
        $searchType = $params['search_type'] ?? 'daily';
        $now = Carbon::now();

        return [
            // Widget 1: Motivational Statistics - Booking comparison with target
            'motivational_stats' => [
                'today' => 245,
                'yesterday' => 227,
                'target' => 300,
                'percentage' => 81.7,
                'remaining' => 55, // target - today
                'growth' => 18, // today - yesterday
                'growth_percentage' => 7.9, // ((today - yesterday) / yesterday) * 100
            ],

            // Widget 2: Statistics Cards - Array of 4 main stats with full presentation data
            'statistics' => [
                [
                    'id' => 'patients',
                    'label' => 'Jumlah Pasien',
                    'count' => 245,
                    'change' => 18,
                    'change_type' => 'increase',
                    'percentage_change' => 7.9,
                    'change_label' => 'Dari kemarin',
                    'icon' => 'mdi:account-group',
                    'color' => 'blue',
                    'gradient' => 'from-blue-500 to-cyan-400',
                    'bg_light' => 'bg-blue-50',
                    'text_color' => 'text-blue-600',
                    'border_color' => 'border-blue-200',
                ],
                [
                    'id' => 'new-patients',
                    'label' => 'Pasien Baru',
                    'count' => 42,
                    'change' => 5,
                    'change_type' => 'increase',
                    'percentage_change' => 13.5,
                    'change_label' => 'Dari kemarin',
                    'icon' => 'mdi:account-plus',
                    'color' => 'purple',
                    'gradient' => 'from-purple-500 to-pink-400',
                    'bg_light' => 'bg-purple-50',
                    'text_color' => 'text-purple-600',
                    'border_color' => 'border-purple-200',
                ],
                [
                    'id' => 'revenue',
                    'label' => 'Omzet',
                    'count' => 15750000, // Rp 15.750.000
                    'change' => 2100000,
                    'change_type' => 'increase',
                    'percentage_change' => 15.4,
                    'change_label' => 'Dari kemarin',
                    'icon' => 'mdi:cash-multiple',
                    'color' => 'emerald',
                    'gradient' => 'from-emerald-500 to-teal-400',
                    'bg_light' => 'bg-emerald-50',
                    'text_color' => 'text-emerald-600',
                    'border_color' => 'border-emerald-200',
                    'is_currency' => true,
                ],
                [
                    'id' => 'unfinished',
                    'label' => 'Tindakan Dipesankan',
                    'count' => 31,
                    'change' => 8,
                    'change_type' => 'increase',
                    'percentage_change' => 34.8,
                    'change_label' => 'Dari kemarin',
                    'icon' => 'mdi:clipboard-list',
                    'color' => 'orange',
                    'gradient' => 'from-orange-500 to-amber-400',
                    'bg_light' => 'bg-orange-50',
                    'text_color' => 'text-orange-600',
                    'border_color' => 'border-orange-200',
                ],
            ],

            // Widget 3: Pending Items - Array of action items with links
            'pending_items' => [
                [
                    'label' => '12 unsigned visits',
                    'count' => 12,
                    'icon' => 'mdi:file-document-edit-outline',
                    'color' => 'text-orange-600',
                    'link' => '/patient-emr/unsigned-visits',
                    'priority' => 'high',
                ],
                [
                    'label' => '5 patients unsynced to Satu Sehat',
                    'count' => 5,
                    'icon' => 'mdi:sync-alert',
                    'color' => 'text-red-600',
                    'link' => '/satu-sehat/dashboard',
                    'priority' => 'critical',
                ],
                [
                    'label' => '3 visits without ICD diagnosis',
                    'count' => 3,
                    'icon' => 'mdi:alert-circle',
                    'color' => 'text-amber-600',
                    'link' => '/patient-emr/incomplete-diagnosis',
                    'priority' => 'medium',
                ],
                [
                    'label' => '8 pending lab results',
                    'count' => 8,
                    'icon' => 'mdi:flask-empty-outline',
                    'color' => 'text-blue-600',
                    'link' => '/laboratory/pending-results',
                    'priority' => 'medium',
                ],
                [
                    'label' => '6 unpaid invoices',
                    'count' => 6,
                    'icon' => 'mdi:receipt-text-outline',
                    'color' => 'text-purple-600',
                    'link' => '/billing/unpaid-invoices',
                    'priority' => 'low',
                ],
                [
                    'label' => '4 prescription refills due',
                    'count' => 4,
                    'icon' => 'mdi:pill',
                    'color' => 'text-green-600',
                    'link' => '/pharmacy/refills',
                    'priority' => 'medium',
                ],
                [
                    'label' => '2 follow-up appointments needed',
                    'count' => 2,
                    'icon' => 'mdi:calendar-clock',
                    'color' => 'text-indigo-600',
                    'link' => '/appointments/follow-ups',
                    'priority' => 'low',
                ],
            ],

            // Widget 4: Queue Services - Array of service queues with current status
            'queue_services' => [
                [
                    'id' => 1,
                    'service' => 'Poli Umum',
                    'current_queue' => 'A-015',
                    'waiting' => 8,
                    'serving' => 4,
                    'completed' => 33,
                    'total_today' => 45,
                    'avg_wait_time' => 18, // minutes
                    'icon' => 'mdi:stethoscope',
                    'color' => 'blue',
                    'bg_color' => 'bg-blue-50',
                    'text_color' => 'text-blue-600',
                    'border_color' => 'border-blue-200',
                ],
                [
                    'id' => 2,
                    'service' => 'Poli Gigi',
                    'current_queue' => 'B-007',
                    'waiting' => 3,
                    'serving' => 2,
                    'completed' => 13,
                    'total_today' => 18,
                    'avg_wait_time' => 25,
                    'icon' => 'mdi:tooth',
                    'color' => 'cyan',
                    'bg_color' => 'bg-cyan-50',
                    'text_color' => 'text-cyan-600',
                    'border_color' => 'border-cyan-200',
                ],
                [
                    'id' => 3,
                    'service' => 'Poli Anak',
                    'current_queue' => 'C-012',
                    'waiting' => 6,
                    'serving' => 3,
                    'completed' => 28,
                    'total_today' => 37,
                    'avg_wait_time' => 22,
                    'icon' => 'mdi:baby-face-outline',
                    'color' => 'pink',
                    'bg_color' => 'bg-pink-50',
                    'text_color' => 'text-pink-600',
                    'border_color' => 'border-pink-200',
                ],
                [
                    'id' => 4,
                    'service' => 'Laboratorium',
                    'current_queue' => 'L-025',
                    'waiting' => 12,
                    'serving' => 3,
                    'completed' => 52,
                    'total_today' => 67,
                    'avg_wait_time' => 15,
                    'icon' => 'mdi:flask',
                    'color' => 'purple',
                    'bg_color' => 'bg-purple-50',
                    'text_color' => 'text-purple-600',
                    'border_color' => 'border-purple-200',
                ],
                [
                    'id' => 5,
                    'service' => 'Radiologi',
                    'current_queue' => 'R-008',
                    'waiting' => 5,
                    'serving' => 1,
                    'completed' => 16,
                    'total_today' => 22,
                    'avg_wait_time' => 30,
                    'icon' => 'mdi:image-multiple',
                    'color' => 'indigo',
                    'bg_color' => 'bg-indigo-50',
                    'text_color' => 'text-indigo-600',
                    'border_color' => 'border-indigo-200',
                ],
                [
                    'id' => 6,
                    'service' => 'Apotek',
                    'current_queue' => 'F-042',
                    'waiting' => 15,
                    'serving' => 5,
                    'completed' => 89,
                    'total_today' => 109,
                    'avg_wait_time' => 12,
                    'icon' => 'mdi:pill',
                    'color' => 'green',
                    'bg_color' => 'bg-green-50',
                    'text_color' => 'text-green-600',
                    'border_color' => 'border-green-200',
                ],
            ],

            // Widget 5: Diagnosis & Treatment Records - Array of patient records
            'diagnosis_treatment' => [
                [
                    'patient' => 'Budi Santoso',
                    'patient_id' => 'P001234',
                    'age' => 45,
                    'gender' => 'L',
                    'code' => 'A09',
                    'type' => 'Diagnosa',
                    'description' => 'Gastroenteritis',
                    'poli' => 'Poli Umum',
                    'date' => $now->format('Y-m-d'),
                    'time' => '08:30',
                    'doctor' => 'Dr. Ahmad Wijaya, Sp.PD',
                    'status' => 'completed',
                    'queue_number' => 'A-001',
                ],
                [
                    'patient' => 'Siti Nurhaliza',
                    'patient_id' => 'P001235',
                    'age' => 8,
                    'gender' => 'P',
                    'code' => 'J06.9',
                    'type' => 'Diagnosa',
                    'description' => 'ISPA (Infeksi Saluran Pernapasan Akut)',
                    'poli' => 'Poli Anak',
                    'date' => $now->format('Y-m-d'),
                    'time' => '09:15',
                    'doctor' => 'Dr. Sari Kusuma, Sp.A',
                    'status' => 'completed',
                    'queue_number' => 'C-002',
                ],
                [
                    'patient' => 'Andi Wijaya',
                    'patient_id' => 'P001236',
                    'age' => 32,
                    'gender' => 'L',
                    'code' => 'T-001',
                    'type' => 'Tindakan',
                    'description' => 'Pencabutan Gigi',
                    'poli' => 'Poli Gigi',
                    'date' => $now->format('Y-m-d'),
                    'time' => '10:00',
                    'doctor' => 'Dr. Dewi Lestari, Sp.KG',
                    'status' => 'in_progress',
                    'queue_number' => 'B-003',
                ],
                [
                    'patient' => 'Rina Kusuma',
                    'patient_id' => 'P001237',
                    'age' => 56,
                    'gender' => 'P',
                    'code' => 'E11.9',
                    'type' => 'Diagnosa',
                    'description' => 'Diabetes Mellitus Tipe 2',
                    'poli' => 'Poli Umum',
                    'date' => $now->format('Y-m-d'),
                    'time' => '10:45',
                    'doctor' => 'Dr. Ahmad Wijaya, Sp.PD',
                    'status' => 'completed',
                    'queue_number' => 'A-004',
                ],
                [
                    'patient' => 'Joko Susilo',
                    'patient_id' => 'P001238',
                    'age' => 41,
                    'gender' => 'L',
                    'code' => 'T-002',
                    'type' => 'Tindakan',
                    'description' => 'Rontgen Dada',
                    'poli' => 'Radiologi',
                    'date' => $now->format('Y-m-d'),
                    'time' => '11:20',
                    'doctor' => 'Dr. Putri Ayu, Sp.Rad',
                    'status' => 'completed',
                    'queue_number' => 'R-005',
                ],
                [
                    'patient' => 'Lina Marlina',
                    'patient_id' => 'P001239',
                    'age' => 52,
                    'gender' => 'P',
                    'code' => 'I10',
                    'type' => 'Diagnosa',
                    'description' => 'Hipertensi',
                    'poli' => 'Poli Umum',
                    'date' => $now->format('Y-m-d'),
                    'time' => '13:00',
                    'doctor' => 'Dr. Ahmad Wijaya, Sp.PD',
                    'status' => 'completed',
                    'queue_number' => 'A-006',
                ],
                [
                    'patient' => 'Tommy Hermawan',
                    'patient_id' => 'P001240',
                    'age' => 38,
                    'gender' => 'L',
                    'code' => 'K29.7',
                    'type' => 'Diagnosa',
                    'description' => 'Gastritis',
                    'poli' => 'Poli Umum',
                    'date' => $now->format('Y-m-d'),
                    'time' => '14:15',
                    'doctor' => 'Dr. Sari Kusuma, Sp.A',
                    'status' => 'waiting_lab',
                    'queue_number' => 'A-007',
                ],
                [
                    'patient' => 'Mega Wulandari',
                    'patient_id' => 'P001241',
                    'age' => 29,
                    'gender' => 'P',
                    'code' => 'T-003',
                    'type' => 'Tindakan',
                    'description' => 'Pemeriksaan USG',
                    'poli' => 'Radiologi',
                    'date' => $now->format('Y-m-d'),
                    'time' => '15:00',
                    'doctor' => 'Dr. Putri Ayu, Sp.Rad',
                    'status' => 'in_progress',
                    'queue_number' => 'R-008',
                ],
                [
                    'patient' => 'Doni Prasetyo',
                    'patient_id' => 'P001242',
                    'age' => 12,
                    'gender' => 'L',
                    'code' => 'B86',
                    'type' => 'Diagnosa',
                    'description' => 'Scabies',
                    'poli' => 'Poli Anak',
                    'date' => $now->format('Y-m-d'),
                    'time' => '15:30',
                    'doctor' => 'Dr. Sari Kusuma, Sp.A',
                    'status' => 'completed',
                    'queue_number' => 'C-009',
                ],
                [
                    'patient' => 'Ratna Sari',
                    'patient_id' => 'P001243',
                    'age' => 47,
                    'gender' => 'P',
                    'code' => 'M79.3',
                    'type' => 'Diagnosa',
                    'description' => 'Nyeri Punggung',
                    'poli' => 'Poli Umum',
                    'date' => $now->format('Y-m-d'),
                    'time' => '16:00',
                    'doctor' => 'Dr. Ahmad Wijaya, Sp.PD',
                    'status' => 'completed',
                    'queue_number' => 'A-010',
                ],
            ],

            // Metadata
            'meta' => [
                'period_type' => $searchType,
                'timestamp' => $now->toIso8601String(),
                'date' => $now->format('Y-m-d'),
                'year' => $now->year,
                'month' => $now->month,
                'day' => $now->day,
                'data_source' => 'dummy',
                'version' => '2.0.0',
            ],
        ];
    }
}
