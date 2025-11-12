<?php

namespace Projects\WellmedGateway\Controllers\API\PatientEmr\VisitRegistration\VisitExamination\Examination;

use Projects\WellmedGateway\Requests\API\PatientEmr\VisitRegistration\VisitExamination\Examination\{
    StoreRequest, UpdateRequest
};
use Projects\WellmedGateway\Controllers\API\PatientEmr\VisitExamination\Examination\EnvironmentController;

class ExaminationController extends EnvironmentController
{
    public function store(StoreRequest $request){
        return $this->storeExamination();
    }

    // public function update(UpdateRequest $request){
    //     $this->setPractitionerId();
        
    //     $this->__examination_schema->commitExamination();

    //     $visitExamination  = $this->VisitExaminationModel()->find(request()->visit_examination_id);
    //     $visitRegistration = $visitExamination->visitRegistration;

    //     if(!isset($visitRegistration->head_doctor_id)) {
    //         // selain dari perawat boleh jadi head docter
    //         if(isset(auth()->user()->userReference->reference->profession) && auth()->user()->userReference->reference->profession->parent_id != 8){
    //             $visitRegistration->head_doctor_id   = auth()->user()->userReference->reference->id;
    //             $visitRegistration->head_doctor_type = $this->EmployeeModel()->getMorphClass();
    //             $visitRegistration->save();
    //         }
    //     }

    //     // Update McuReportSummary
    //     $transaction = $visitRegistration->transaction;
    //     $parent_id = $transaction->parent_id;
    //     $transaction_mcu_service_price = $this->TransactionModel()->where('parent_id',$parent_id)->where('reference_type','McuServicePrice')->first();

    //     if(isset($transaction_mcu_service_price)){
    //         $transaction_mcu_service_price->reported_at = null;
    //         $transaction_mcu_service_price->is_rendered = null;
    //         $transaction_mcu_service_price->save();

    //         $transaction_mcu_service_price->reported_at = now();
    //         $transaction_mcu_service_price->save();
    //     }
    // }
}
