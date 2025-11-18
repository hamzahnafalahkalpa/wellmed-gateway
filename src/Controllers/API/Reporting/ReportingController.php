<?php

namespace Projects\WellmedGateway\Controllers\API\Reporting;

use Hanafalah\LaravelSupport\Concerns\Support\HasCache;
use Illuminate\Http\Request;
use Projects\WellmedGateway\Controllers\API\ApiController;
use Illuminate\Support\Str;

class ReportingController extends ApiController{
    use HasCache;

    protected $__onlies = [
    ];

    protected $__stores = [
    ];

    public function index(Request $request){
        request()->merge([ 
            'search_name'  => request()->search_name ?? request()->search_value,
            'type' => 'paginate', 
            'search_value' => null
        ]);
        $morph = Str::upper(Str::replace('-','_',request()->reporting_type));
        switch ($morph) {
            case 'PATIENT_DATA_RECAP_REPORT':
                $morph = 'Patient';
                return $this->callAutolist($morph);
            break;
            case 'VISIT_PATIENT_REPORT':
                $morph = 'VisitPatient';
                return $this->callAutolist($morph);
            break;
            default:
                return $this->callAutolist($morph);
            break;
        }
    }

    private function callAutolist(string $morph,?callable $callback = null){
        return app(config('app.contracts.'.$morph))->autolist(request()->type,$callback);
    }
}