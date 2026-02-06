<?php

namespace Projects\WellmedGateway\Controllers\API\Reporting;

use Hanafalah\LaravelSupport\Concerns\Support\HasCache;
use Illuminate\Http\Request;
use Projects\WellmedGateway\Controllers\API\ApiController;
use Illuminate\Support\Str;
use Projects\WellmedGateway\Schemas\Reporting;

class ReportingController extends ApiController
{
    use HasCache;

    protected ?Reporting $reporting = null;

    public function __construct()
    {
        $this->reporting = app(Reporting::class);
    }

    /**
     * Main report endpoint - routes to specific report type
     */
    public function index(Request $request)
    {
        $reportType = Str::upper(Str::replace('-', '_', request()->reporting_type));
        $page = request()->page ?? 1;
        $perPage = request()->per_page ?? 10;

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
     */
    protected function patientRecap(int $page, int $perPage): array
    {
        $filters = $this->extractFilters(['medical_record', 'name', 'people.card_identity.nik', 'people.pob', 'people.dob']);

        $result = $this->reporting->search(Reporting::INDEX_PATIENT, [
            'page' => $page,
            'per_page' => $perPage,
            'filters' => $filters,
        ]);

        return array_merge($result, [
            'attributes' => [],
            'filters' => $this->getPatientFilters(),
            'columns' => $this->getPatientColumns(),
        ]);
    }

    /**
     * Visit Patient Recap Report
     */
    protected function visitPatientRecap(int $page, int $perPage): array
    {
        $filters = $this->extractFilters(['patient.name', 'visit_code', 'medic_service.name']);

        $result = $this->reporting->search(Reporting::INDEX_VISIT_PATIENT, [
            'page' => $page,
            'per_page' => $perPage,
            'filters' => $filters,
        ]);

        return array_merge($result, [
            'attributes' => [],
            'filters' => $this->getVisitPatientFilters(),
            'columns' => $this->getVisitPatientColumns(),
        ]);
    }

    /**
     * Diagnosis Recap Report
     */
    protected function diagnosisRecap(int $page, int $perPage): array
    {
        $filters = $this->extractFilters(['disease_name', 'patient.name']);

        $result = $this->reporting->search(Reporting::INDEX_PATIENT_ILLNESS, [
            'page' => $page,
            'per_page' => $perPage,
            'filters' => $filters,
        ]);

        return array_merge($result, [
            'attributes' => [],
            'filters' => $this->getDiagnosisFilters(),
            'columns' => $this->getDiagnosisColumns(),
        ]);
    }

    /**
     * Billing Recap Report
     */
    protected function billingRecap(int $page, int $perPage): array
    {
        $filters = $this->extractFilters(['billing_code', 'patient.name', 'status']);

        $result = $this->reporting->search(Reporting::INDEX_BILLING, [
            'page' => $page,
            'per_page' => $perPage,
            'filters' => $filters,
        ]);

        return array_merge($result, [
            'attributes' => [],
            'filters' => $this->getBillingFilters(),
            'columns' => $this->getBillingColumns(),
        ]);
    }

    /**
     * Payment Recap Report
     */
    protected function paymentRecap(int $page, int $perPage): array
    {
        $filters = $this->extractFilters(['invoice_code', 'patient.name', 'payment_method']);

        $result = $this->reporting->search(Reporting::INDEX_INVOICE, [
            'page' => $page,
            'per_page' => $perPage,
            'filters' => $filters,
        ]);

        return array_merge($result, [
            'attributes' => [],
            'filters' => $this->getPaymentFilters(),
            'columns' => $this->getPaymentColumns(),
        ]);
    }

    /**
     * Medic Observation Recap Report
     */
    protected function medicObservationRecap(int $page, int $perPage): array
    {
        $filters = $this->extractFilters(['patient.name', 'observation_type']);

        $result = $this->reporting->search(Reporting::INDEX_VISIT_EXAMINATION, [
            'page' => $page,
            'per_page' => $perPage,
            'filters' => $filters,
        ]);

        return array_merge($result, [
            'attributes' => [],
            'filters' => $this->getMedicObservationFilters(),
            'columns' => $this->getMedicObservationColumns(),
        ]);
    }

    /**
     * Refund/Discount Recap Report
     */
    protected function refundDiscountRecap(int $page, int $perPage): array
    {
        $filters = $this->extractFilters(['refund_code', 'patient.name', 'type']);

        $result = $this->reporting->search(Reporting::INDEX_REFUND, [
            'page' => $page,
            'per_page' => $perPage,
            'filters' => $filters,
        ]);

        return array_merge($result, [
            'attributes' => [],
            'filters' => $this->getRefundFilters(),
            'columns' => $this->getRefundColumns(),
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
     * Extract filters from request based on allowed keys
     */
    protected function extractFilters(array $allowedKeys): array
    {
        $filters = [];
        foreach ($allowedKeys as $key) {
            $value = request()->input($key) ?? request()->input(str_replace('.', '_', $key));
            if (!empty($value)) {
                $filters[$key] = $value;
            }
        }
        return $filters;
    }

    // ===== Filter Definitions =====

    protected function getPatientFilters(): array
    {
        return [
            ['label' => 'No. RM', 'key' => 'medical_record', 'type' => 'InputText'],
            ['label' => 'NIK', 'key' => 'people.card_identity.nik', 'type' => 'InputText'],
            ['label' => 'Nama Pasien', 'key' => 'name', 'type' => 'InputText'],
            ['label' => 'Tempat Lahir', 'key' => 'people.pob', 'type' => 'InputText'],
            ['label' => 'Tanggal Lahir', 'key' => 'people.dob', 'type' => 'InputText'],
        ];
    }

    protected function getVisitPatientFilters(): array
    {
        return [
            ['label' => 'Nama Pasien', 'key' => 'patient.name', 'type' => 'InputText'],
            ['label' => 'Kode Kunjungan', 'key' => 'visit_code', 'type' => 'InputText'],
            ['label' => 'Layanan', 'key' => 'medic_service.name', 'type' => 'InputText'],
        ];
    }

    protected function getDiagnosisFilters(): array
    {
        return [
            ['label' => 'Nama Diagnosis', 'key' => 'disease_name', 'type' => 'InputText'],
            ['label' => 'Nama Pasien', 'key' => 'patient.name', 'type' => 'InputText'],
        ];
    }

    protected function getBillingFilters(): array
    {
        return [
            ['label' => 'Kode Billing', 'key' => 'billing_code', 'type' => 'InputText'],
            ['label' => 'Nama Pasien', 'key' => 'patient.name', 'type' => 'InputText'],
            ['label' => 'Status', 'key' => 'status', 'type' => 'InputText'],
        ];
    }

    protected function getPaymentFilters(): array
    {
        return [
            ['label' => 'Kode Invoice', 'key' => 'invoice_code', 'type' => 'InputText'],
            ['label' => 'Nama Pasien', 'key' => 'patient.name', 'type' => 'InputText'],
            ['label' => 'Metode Pembayaran', 'key' => 'payment_method', 'type' => 'InputText'],
        ];
    }

    protected function getMedicObservationFilters(): array
    {
        return [
            ['label' => 'Nama Pasien', 'key' => 'patient.name', 'type' => 'InputText'],
            ['label' => 'Tipe Observasi', 'key' => 'observation_type', 'type' => 'InputText'],
        ];
    }

    protected function getRefundFilters(): array
    {
        return [
            ['label' => 'Kode Refund', 'key' => 'refund_code', 'type' => 'InputText'],
            ['label' => 'Nama Pasien', 'key' => 'patient.name', 'type' => 'InputText'],
            ['label' => 'Tipe', 'key' => 'type', 'type' => 'InputText'],
        ];
    }

    // ===== Column Definitions =====

    protected function getPatientColumns(): array
    {
        return [
            ['key' => 'medical_record', 'label' => 'No RM'],
            ['key' => 'people.card_identity.nik', 'label' => 'NIK'],
            ['key' => 'name', 'label' => 'Nama Pasien'],
            ['key' => 'patient_type.name', 'label' => 'Jenis Pasien'],
            ['key' => 'people.phone_1', 'label' => 'Kontak 1'],
            ['key' => 'people.phone_2', 'label' => 'Kontak 2'],
            ['key' => 'people.age', 'label' => 'Usia'],
            ['key' => 'people.sex', 'label' => 'Jenis Kelamin'],
            ['key' => 'people.pob', 'label' => 'Tempat Lahir'],
            ['key' => 'people.dob', 'label' => 'Tanggal Lahir'],
        ];
    }

    protected function getVisitPatientColumns(): array
    {
        return [
            ['key' => 'visit_code', 'label' => 'Kode Kunjungan'],
            ['key' => 'patient.name', 'label' => 'Nama Pasien'],
            ['key' => 'patient.medical_record', 'label' => 'No RM'],
            ['key' => 'medic_service.name', 'label' => 'Layanan'],
            ['key' => 'visit_date', 'label' => 'Tanggal Kunjungan'],
            ['key' => 'status', 'label' => 'Status'],
        ];
    }

    protected function getDiagnosisColumns(): array
    {
        return [
            ['key' => 'name', 'label' => 'Nama Diagnosis'],
            ['key' => 'disease.code', 'label' => 'Kode Diagnosis'],
            ['key' => 'classification_disease.name', 'label' => 'Klasifikasi Penyakit'],
            ['key' => 'classification_disease.code', 'label' => 'Kode Klasifikasi Penyakit'],
            ['key' => 'patient.name', 'label' => 'Nama Pasien'],
            ['key' => 'patient.people.sex', 'label' => 'Jenis Kelamin'],
            ['key' => 'patient.people.dob', 'label' => 'Tanggal Lahir'],
            ['key' => 'created_at', 'label' => 'Tanggal Dibuat'],
        ];
    }

    protected function getBillingColumns(): array
    {
        return [
            ['key' => 'billing_code', 'label' => 'Kode Billing'],
            ['key' => 'patient.name', 'label' => 'Nama Pasien'],
            ['key' => 'total_amount', 'label' => 'Total'],
            ['key' => 'status', 'label' => 'Status'],
            ['key' => 'created_at', 'label' => 'Tanggal'],
        ];
    }

    protected function getPaymentColumns(): array
    {
        return [
            ['key' => 'invoice_code', 'label' => 'Kode Invoice'],
            ['key' => 'patient.name', 'label' => 'Nama Pasien'],
            ['key' => 'total_amount', 'label' => 'Total Pembayaran'],
            ['key' => 'payment_method', 'label' => 'Metode Pembayaran'],
            ['key' => 'paid_at', 'label' => 'Tanggal Bayar'],
        ];
    }

    protected function getMedicObservationColumns(): array
    {
        return [
            ['key' => 'patient.name', 'label' => 'Nama Pasien'],
            ['key' => 'observation_type', 'label' => 'Tipe Observasi'],
            ['key' => 'observation_value', 'label' => 'Nilai'],
            ['key' => 'observed_at', 'label' => 'Waktu Observasi'],
        ];
    }

    protected function getRefundColumns(): array
    {
        return [
            ['key' => 'refund_code', 'label' => 'Kode Refund'],
            ['key' => 'patient.name', 'label' => 'Nama Pasien'],
            ['key' => 'type', 'label' => 'Tipe'],
            ['key' => 'amount', 'label' => 'Jumlah'],
            ['key' => 'reason', 'label' => 'Alasan'],
            ['key' => 'created_at', 'label' => 'Tanggal'],
        ];
    }
}
