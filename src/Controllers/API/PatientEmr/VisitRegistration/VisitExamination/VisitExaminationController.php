<?php

namespace Projects\WellmedGateway\Controllers\API\PatientEmr\VisitRegistration\VisitExamination;

use Projects\WellmedGateway\Requests\API\PatientEmr\Patient\VisitPatient\VisitRegistration\VisitExamination\{
    ViewRequest, ShowRequest, StoreRequest, DeleteRequest
};
use Projects\WellmedGateway\Controllers\API\PatientEmr\VisitExamination\EnvironmentController;

class VisitExaminationController extends EnvironmentController
{
    protected function commonRequest(){
        parent::commonRequest();
        request()->merge([
            'search_visit_registration_id' => request()->visit_registration_id
        ]);
    }

    public function index(ViewRequest $request){
        return $this->getVisitExaminationPaginate();
    }

    public function show(ShowRequest $request){
        return $this->showVisitExamination();
    }

    public function store(StoreRequest $request){
        $this->commonRequest();
        $practitioner_evaluations = request()->practitioner_evaluations;
        if (!isset($practitioner_evaluations) || count($practitioner_evaluations) > 0){
            $practitioner_evaluations = [
                "practitioner_type" => "Employee", //nullable, default from config
                "practitioner_id"=> $this->global_employee->getKey(), //GET FROM AUTOLIST - EMPLOYEE LIST (DOCTOR)
            ];
            request()->merge([
                'practitioner_evaluations' => [$practitioner_evaluations]
            ]);
        }
        return $this->storeVisitExamination();
    }

    public function destroy(DeleteRequest $request){
        return $this->deleteVisitExamination();
    }
}
