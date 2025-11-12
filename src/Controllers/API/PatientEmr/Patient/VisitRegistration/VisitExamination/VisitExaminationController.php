<?php

namespace Projects\WellmedGateway\Controllers\API\PatientEmr\Patient\VisitRegistration\VisitExamination;

use Projects\WellmedGateway\Requests\API\PatientEmr\Patient\VisitRegistration\VisitExamination\{
    ViewRequest, ShowRequest, StoreRequest, DeleteRequest
};
use Projects\WellmedGateway\Controllers\API\PatientEmr\VisitExamination\EnvironmentController;

class VisitExaminationController extends EnvironmentController
{
    public function index(ViewRequest $request){
        return $this->__visit_examination_schema->viewVisitExaminationPaginate();
    }

    public function show(ShowRequest $request){
        return $this->__visit_examination_schema->showVisitExamination();
    }

    public function store(StoreRequest $request){
        return $this->storeVisitExamination();
    }

    public function destroy(DeleteRequest $request){
        return $this->deleteVisitExamination();
    }

    // public function done(CompateRequest $request) {
    //     return $this->transaction(function () {
    //         $data = $this->__visit_examination_schema->visitExaminationDoneProcess();
    //         $assessments = $this->AssessmentModel()->whereIn('morph', ['Anthropometry', 'PrimaryDiagnose', 'SecondaryDiagnose'])->where('visit_examination_id', $data->id)->get();
    //         // dispatch(new SendSatuSehatJob($data, $assessments, "MCU"));
    //         return $data;
    //     });
    // }

    // public function cancel(CompateRequest $request) {
    //     if(!isset(request()->password_cancel)) throw new \Exception("perlu masukan password");
    //     if(Hash::check(request()->password_cancel,$this->global_workspace->password_cancel)) {
    //         $visit_patient = $this->__visit_examination_schema->visitExaminationCancelation();
    //         $transaction = $this->TransactionModel()->with(['transactionItems'])->where('reference_type', 'VisitPatient')->where('reference_id', $visit_patient->id)->first();
    //         // dispatch(new RequestLabToLISJob($visit_patient, 'cancel', $transaction));
    //         return true;
    //     }else throw new \Exception("password salah!");
    // }
}
