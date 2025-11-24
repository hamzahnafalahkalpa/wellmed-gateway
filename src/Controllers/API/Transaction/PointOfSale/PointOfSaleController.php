<?php

namespace Projects\WellmedGateway\Controllers\API\Transaction\PointOfSale;

use Projects\WellmedGateway\Controllers\API\Transaction\PointOfSale\EnvironmentController;
use Projects\WellmedGateway\Requests\API\Transaction\PointOfSale\{
    ViewRequest, ShowRequest, StoreRequest, DeleteRequest
};

class PointOfSaleController extends EnvironmentController{
    protected function commonRequest(){
        parent::commonRequest();
        $this->userAttempt();
        $billing = request()?->billing;
        if (isset($billing)){
            $billing['author_type']  ??= $this->global_employee->getMorphClass();   
            $billing['author_id']    ??= $this->global_employee->getKey();   
            $billing['cashier_type'] ??= $this->global_room?->getMorphClass() ?? 'Room';   
            $billing['cashier_id']   ??= $this->global_room?->getKey();   
        }
        request()->merge([
            'search_reference_type' => ['VisitPatient','PaymentSummary'],
            'billing'               => $billing ?? null
        ]);
    }

    public function index(ViewRequest $request){
        return $this->getPosTransactionPaginate();
    }

    public function show(ShowRequest $request){
        return $this->showPosTransaction();
    }

    public function store(StoreRequest $request){
        $possibleTypes = ['pharmacy_sale','visit_patient','submission'];

        $reference = null;
        $referenceType = null;

        foreach ($possibleTypes as $type) {
            if (request()->filled($type)) {
                $reference = request()->input($type);
                $referenceType = $type;
                switch ($type) {
                    case 'visit_patient':
                    case 'pharmacy_sale':
                        $reference['visit_registration'] = $this->mergeArray($reference['visit_registration'],[
                            "practitioner_evaluation" => [
                                "id" => null,
                                "practitioner_type" => $this->global_employee->getMorphClass(), 
                                "practitioner_id" => $this->global_employee->getKey(),
                                "as_pic" => true
                            ]
                        ]);
                    break;
                    default:
                        # code...
                    break;
                }
                break;
            }
        }

        $data = array_fill_keys($possibleTypes, null);
        $data['reference'] = $reference;
        $data['reference_type'] = $referenceType;
        request()->merge($data);
        $transaction = $this->storePosTransaction();
        $this->elasticBillingIndexing($transaction['billing']['id']);
        return $transaction;
    }

    public function delete(DeleteRequest $request){
        return $this->deletePosTransaction();
    }
}