<?php

namespace Projects\WellmedGateway\Controllers\API\Reporting;

use Hanafalah\LaravelSupport\Concerns\Support\HasCache;
use Illuminate\Http\Request;
use Projects\WellmedGateway\Controllers\API\ApiController;
use Illuminate\Support\Str;
use Projects\WellmedGateway\Controllers\API\Reporting\Report\{
    HasPatientRecap, HasVisitPatientRecap, HasBillingRecap, HasPaymentRecap,
    HasMedicObservationRecap, HasRefundDiscountRecap, HasDiagnosisRecap
};

class ReportingController extends ApiController{
    use HasCache, HasPatientRecap, HasVisitPatientRecap, HasBillingRecap,
        HasPaymentRecap, HasMedicObservationRecap,
        HasRefundDiscountRecap, HasDiagnosisRecap;

    protected $__client;
    protected $__response;

    public function index(Request $request){
        request()->merge([ 
            'search_name'  => request()->search_name ?? request()->search_value,
            'type' => 'paginate', 
            'search_value' => null
        ]);
        $morph = Str::upper(Str::replace('-','_',request()->reporting_type));
        $this->__client = config('app.elasticsearch.client');
        $page = request()->page ?? 1;
        $size = request()->per_page ?? 10;
        $from = ($page - 1) * $size + 1;
        $this->__response = [
            "attributes" => [
                // "groupRowsBy" => 'visit_code'
            ],
            "data" => [],
            "from" => $from,
            "to" => $from + $size,
            "total" => 0,
            "per_page" => $size,
            "current_page" => $page,
            "last_page" => 19
        ];

        switch ($morph) {
            case 'PATIENT_DATA_RECAP_REPORT'      : return $this->patientRecap();break;
            case 'VISIT_PATIENT_REPORT'           : return $this->visitPatientRecap();break;
            case "TRANSACTION_BILLING_REPORT"     : return $this->billingRecap();break;
            case "PAYMENT_RECAP_REPORT"           : return $this->paymentRecap();break;
            case "MEDIC_OBSERVATION_RECAP_REPORT" : return $this->medicObservationRecap();break;
            case "REFUND_DISCOUNT_RECAP_REPORT"   : return $this->refundDiscountRecap();break;
            case "DIAGNOSIS_RECAP_REPORT"         : return $this->diagnosisRecap();break;
            default:
                return $this->callAutolist($morph);
            break;
        }
    }

    private function resolveForPaginate(array &$paginate, object|array &$data){
        $data = $data->asArray();
        $hits = &$data['hits']; 
        $paginate['total'] = $hits['total']['value'] ?? 0;
        $data_hits = $hits['hits'] ?? [];
        if (count($data_hits) > 0) {
            foreach ($data_hits as &$hit) {
                $source = $hit['_source'] ?? [];
                $paginate['data'][] = $source;
            }
        }
        $paginate['last_page'] = ceil($paginate['total'] / $paginate['per_page']);
    }
}