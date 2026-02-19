<?php

namespace Projects\WellmedGateway\Controllers\API\Reporting;

use Hanafalah\LaravelSupport\Concerns\Support\HasCache;
use Illuminate\Http\Request;
use Projects\WellmedGateway\Controllers\API\ApiController;
use Illuminate\Support\Str;

/**
 * ReportingController - Uses database models with ElasticSearch filtering
 *
 * This controller uses the withElasticSearch() scope on models to filter data.
 * The scope uses ES for fast ID lookups, then hydrates from database.
 */
class ReportingController extends ApiController
{
    use HasCache;

    /**
     * Main report endpoint - routes to specific report type
     */
    public function index(Request $request)
    {
        $this->userAttempt();

        $reportType = Str::upper(Str::replace('-', '_', request()->reporting_type));
        $page = request()->page ?? 1;
        $perPage = request()->per_page ?? request()->limit ?? 10;

        return match ($reportType) {
            'PATIENT_DATA_RECAP_REPORT' => $this->patientRecap($page, $perPage),
            'VISIT_PATIENT_REPORT' => $this->visitPatientRecap($page, $perPage),
            'DIAGNOSIS_RECAP_REPORT' => $this->diagnosisRecap($page, $perPage),
            'TRANSACTION_BILLING_REPORT' => $this->billingRecap($page, $perPage),
            'PAYMENT_RECAP_REPORT' => $this->paymentRecap($page, $perPage),
            'MEDIC_OBSERVATION_RECAP_REPORT' => $this->medicObservationRecap($page, $perPage),
            'REFUND_DISCOUNT_RECAP_REPORT' => $this->refundDiscountRecap($page, $perPage),
            default => $this->genericReport($reportType, $page, $perPage),
        };
    }

    /**
     * Patient Data Recap Report
     *
     * Elastic fields: id, name, medical_record, old_mr, first_name, last_name, dob, pob,
     * nik, nik_ibu, passport, reference_type, reference_id, patient_type_id,
     * patient_occupation_id, patient_occupation_name, payer_name, created_at, updated_at
     */
    protected function patientRecap(int $page, int $perPage): array
    {
        $this->mergeSearchParams([
            'medical_record' => request()->input('medical_record'),
            'name' => request()->input('name'),
            'nik' => request()->input('nik'),
            'pob' => request()->input('pob'),
            'dob' => request()->input('dob'),
        ]);

        $model = $this->PatientModel();
        $query = $model->with($model->viewUsingRelation())
            ->withElasticSearch('or');

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        // Get columns from MasterReport database
        $columns = $this->getReportColumns('PATIENT_DATA_RECAP_REPORT');

        return $this->formatPaginatedResponse($paginator, [
            'filters' => $this->getPatientFilters(),
            'columns' => $columns,
        ]);
    }

    /**
     * Visit Patient Recap Report
     *
     * Elastic fields: id, visit_code, queue_number, flag, status, patient_id, name,
     * medical_record, nik, dob, reference_type, reference_id, reservation_id,
     * patient_type_service_id, consument_name, consument_phone, payer_name,
     * medic_service_label, warehouse_name, visited_at, reported_at, created_at, updated_at
     */
    protected function visitPatientRecap(int $page, int $perPage): array
    {
        $this->mergeSearchParams([
            'name' => request()->input('patient_name'),
            'medical_record' => request()->input('medical_record'),
            'visit_code' => request()->input('visit_code'),
            'medic_service_label' => request()->input('medic_service_label'),
            'status' => request()->input('status'),
        ]);

        $model = $this->VisitPatientModel();
        $query = $model->with($model->viewUsingRelation())
            ->withElasticSearch('or');

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        // Get columns from MasterReport database
        $columns = $this->getReportColumns('VISIT_PATIENT_REPORT');

        return $this->formatPaginatedResponse($paginator, [
            'filters' => $this->getVisitPatientFilters(),
            'columns' => $columns,
        ]);
    }

    /**
     * Diagnosis Recap Report
     *
     * Elastic fields: id, name, patient_id, disease_type, disease_id, disease_name,
     * classification_disease_id, reference_type, reference_id, examination_summary_id,
     * patient_summary_id, created_at, updated_at
     */
    protected function diagnosisRecap(int $page, int $perPage): array
    {
        $this->mergeSearchParams([
            'disease_name' => request()->input('disease_name'),
            'name' => request()->input('patient_name'),
            'patient_id' => request()->input('patient_id'),
        ]);

        $model = $this->PatientIllnessModel();
        $query = $model->with($model->viewUsingRelation())
            ->withElasticSearch('or');

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        // Get columns from MasterReport database
        $columns = $this->getReportColumns('DIAGNOSIS_RECAP_REPORT');

        return $this->formatPaginatedResponse($paginator, [
            'filters' => $this->getDiagnosisFilters(),
            'columns' => $columns,
        ]);
    }

    /**
     * Billing Recap Report
     *
     * Elastic fields: id, uuid, billing_code, has_transaction_id, author_type, author_id,
     * author_name, cashier_type, cashier_id, cashier_name, patient_name, patient_nik,
     * consument_name, total_amount, total_paid, total_debt, status, reported_at, created_at, updated_at
     */
    protected function billingRecap(int $page, int $perPage): array
    {
        $this->mergeSearchParams([
            'billing_code' => request()->input('billing_code'),
            'patient_name' => request()->input('patient_name'),
            'patient_nik' => request()->input('patient_nik'),
            'consument_name' => request()->input('consument_name'),
            'status' => request()->input('status'),
        ]);

        $model = $this->BillingModel();
        $query = $model->with($model->viewUsingRelation())
            ->withElasticSearch('or');

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        // Get columns from MasterReport database
        $columns = $this->getReportColumns('TRANSACTION_BILLING_REPORT');

        return $this->formatPaginatedResponse($paginator, [
            'filters' => $this->getBillingFilters(),
            'columns' => $columns,
        ]);
    }

    /**
     * Payment Recap Report
     *
     * Elastic fields: id, flag, invoice_code, billing_id, billing_code, author_id, author_type,
     * author_name, payer_id, payer_type, payer_name, patient_name, patient_nik,
     * total_amount, total_paid, total_debt, reported_at, paid_at, created_at, updated_at
     */
    protected function paymentRecap(int $page, int $perPage): array
    {
        $this->mergeSearchParams([
            'invoice_code' => request()->input('invoice_code'),
            'billing_code' => request()->input('billing_code'),
            'patient_name' => request()->input('patient_name'),
            'patient_nik' => request()->input('patient_nik'),
            'payer_name' => request()->input('payer_name'),
        ]);

        $model = $this->InvoiceModel();
        $query = $model->with($model->viewUsingRelation())
            ->withElasticSearch('or');

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        // Get columns from MasterReport database
        $columns = $this->getReportColumns('PAYMENT_RECAP_REPORT');

        return $this->formatPaginatedResponse($paginator, [
            'filters' => $this->getPaymentFilters(),
            'columns' => $columns,
        ]);
    }

    /**
     * Medic Observation Recap Report
     *
     * Elastic fields: id, visit_examination_code, status, visit_patient_id, visit_registration_id,
     * patient_id, patient_name, patient_medical_record, patient_nik, medic_service_label,
     * warehouse_name, is_commit, is_addendum, sign_off_at, created_at, updated_at
     */
    protected function medicObservationRecap(int $page, int $perPage): array
    {
        $this->mergeSearchParams([
            'patient_name' => request()->input('patient_name'),
            'patient_medical_record' => request()->input('patient_medical_record'),
            'visit_examination_code' => request()->input('visit_examination_code'),
        ]);

        $model = $this->VisitExaminationModel();
        $query = $model->with($model->viewUsingRelation())
            ->withElasticSearch('or');

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        // Get columns from MasterReport database
        $columns = $this->getReportColumns('MEDIC_OBSERVATION_RECAP_REPORT');

        return $this->formatPaginatedResponse($paginator, [
            'filters' => $this->getMedicObservationFilters(),
            'columns' => $columns,
        ]);
    }

    /**
     * Refund/Discount Recap Report
     *
     * Elastic fields: id, code, name, invoice_id, invoice_code, patient_name, patient_nik,
     * refund_amount, reason, created_at, updated_at
     */
    protected function refundDiscountRecap(int $page, int $perPage): array
    {
        $this->mergeSearchParams([
            'code' => request()->input('refund_code'),
            'patient_name' => request()->input('patient_name'),
            'patient_nik' => request()->input('patient_nik'),
            'invoice_code' => request()->input('invoice_code'),
        ]);

        $model = $this->RefundModel();
        $query = $model->with($model->viewUsingRelation())
            ->withElasticSearch('or');

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        // Get columns from MasterReport database
        $columns = $this->getReportColumns('REFUND_DISCOUNT_RECAP_REPORT');

        return $this->formatPaginatedResponse($paginator, [
            'filters' => $this->getRefundFilters(),
            'columns' => $columns,
        ]);
    }

    /**
     * Generic report for unknown types
     */
    protected function genericReport(string $reportType, int $page, int $perPage): array
    {
        return [
            'data' => [],
            'from' => 1,
            'to' => 0,
            'total' => 0,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => 0,
            'message' => "Unknown report type: {$reportType}",
        ];
    }

    /**
     * Merge search parameters into request with search_ prefix
     */
    protected function mergeSearchParams(array $params): void
    {
        $searchParams = [];
        foreach ($params as $key => $value) {
            if (!empty($value) || $value === '0' || $value === 0) {
                $searchParams["search_{$key}"] = $value;
            }
        }

        if (!empty($searchParams)) {
            request()->merge($searchParams);
        }
    }

    /**
     * Format paginated response with metadata
     */
    protected function formatPaginatedResponse($paginator, array $meta = []): array
    {
        // Transform each item using toViewApi if available
        $data = $paginator->getCollection()->map(function ($item) {
            if (method_exists($item, 'toViewApi')) {
                return $item->toViewApi()->resolve();
            }
            return $item->toArray();
        })->toArray();

        return array_merge([
            'data' => $data,
            'from' => $paginator->firstItem() ?? 0,
            'to' => $paginator->lastItem() ?? 0,
            'total' => $paginator->total(),
            'per_page' => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'attributes' => [],
        ], $meta);
    }

    /**
     * Get report columns from MasterReport database
     */
    protected function getReportColumns(string $reportLabel): array
    {
        $masterReport = $this->MasterReportModel()
            ->where('label', $reportLabel)
            ->first();

        if (!$masterReport) {
            return [];
        }

        // Get columns from the database record
        return $masterReport->columns ?? [];
    }

    // ===== Filter Definitions =====
    // Keys MUST match the elastic_config field names in each model

    /**
     * Patient filters
     * Model fields: medical_record, name, nik, pob, dob, first_name, last_name, etc.
     */
    protected function getPatientFilters(): array
    {
        return [
            ['label' => 'No. RM', 'key' => 'medical_record', 'type' => 'InputText'],
            ['label' => 'NIK', 'key' => 'nik', 'type' => 'InputText'],
            ['label' => 'Nama Pasien', 'key' => 'name', 'type' => 'InputText'],
            ['label' => 'Tempat Lahir', 'key' => 'pob', 'type' => 'InputText'],
            ['label' => 'Tanggal Lahir', 'key' => 'dob', 'type' => 'DatePicker'],
        ];
    }

    /**
     * VisitPatient filters
     * Model fields: name, medical_record, nik, visit_code, medic_service_label, status, etc.
     */
    protected function getVisitPatientFilters(): array
    {
        return [
            ['label' => 'Nama Pasien', 'key' => 'patient_name', 'type' => 'InputText'],
            ['label' => 'No. RM', 'key' => 'medical_record', 'type' => 'InputText'],
            ['label' => 'Kode Kunjungan', 'key' => 'visit_code', 'type' => 'InputText'],
            ['label' => 'Layanan', 'key' => 'medic_service_label', 'type' => 'InputText'],
            ['label' => 'Status', 'key' => 'status', 'type' => 'InputText'],
        ];
    }

    /**
     * PatientIllness (Diagnosis) filters
     * Model fields: disease_name, name, patient_id
     */
    protected function getDiagnosisFilters(): array
    {
        return [
            ['label' => 'Nama Diagnosis', 'key' => 'disease_name', 'type' => 'InputText'],
            ['label' => 'Nama Pasien', 'key' => 'patient_name', 'type' => 'InputText'],
        ];
    }

    /**
     * Billing filters
     * Model fields: billing_code, patient_name, patient_nik, consument_name, status
     */
    protected function getBillingFilters(): array
    {
        return [
            ['label' => 'Kode Billing', 'key' => 'billing_code', 'type' => 'InputText'],
            ['label' => 'Nama Pasien', 'key' => 'patient_name', 'type' => 'InputText'],
            ['label' => 'NIK', 'key' => 'patient_nik', 'type' => 'InputText'],
            ['label' => 'Status', 'key' => 'status', 'type' => 'InputText'],
        ];
    }

    /**
     * Invoice (Payment) filters
     * Model fields: invoice_code, billing_code, patient_name, patient_nik, payer_name
     */
    protected function getPaymentFilters(): array
    {
        return [
            ['label' => 'Kode Invoice', 'key' => 'invoice_code', 'type' => 'InputText'],
            ['label' => 'Kode Billing', 'key' => 'billing_code', 'type' => 'InputText'],
            ['label' => 'Nama Pasien', 'key' => 'patient_name', 'type' => 'InputText'],
            ['label' => 'NIK', 'key' => 'patient_nik', 'type' => 'InputText'],
            ['label' => 'Payer', 'key' => 'payer_name', 'type' => 'InputText'],
        ];
    }

    /**
     * VisitExamination (Medic Observation) filters
     * Model fields: patient_name, patient_medical_record, visit_examination_code
     */
    protected function getMedicObservationFilters(): array
    {
        return [
            ['label' => 'Nama Pasien', 'key' => 'patient_name', 'type' => 'InputText'],
            ['label' => 'No. RM', 'key' => 'patient_medical_record', 'type' => 'InputText'],
            ['label' => 'Kode Pemeriksaan', 'key' => 'visit_examination_code', 'type' => 'InputText'],
        ];
    }

    /**
     * Refund filters
     * Model fields: code, patient_name, patient_nik, invoice_code
     */
    protected function getRefundFilters(): array
    {
        return [
            ['label' => 'Kode Refund', 'key' => 'refund_code', 'type' => 'InputText'],
            ['label' => 'Kode Invoice', 'key' => 'invoice_code', 'type' => 'InputText'],
            ['label' => 'Nama Pasien', 'key' => 'patient_name', 'type' => 'InputText'],
            ['label' => 'NIK', 'key' => 'patient_nik', 'type' => 'InputText'],
        ];
    }
}
