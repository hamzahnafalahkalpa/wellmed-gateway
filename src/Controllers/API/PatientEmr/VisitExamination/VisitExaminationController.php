<?php

namespace Projects\WellmedGateway\Controllers\API\PatientEmr\VisitExamination;

use Projects\WellmedGateway\Requests\API\PatientEmr\VisitExamination\{
    ViewRequest, ShowRequest, StoreRequest, DeleteRequest
};
use Illuminate\Support\Facades\Hash;
use Projects\WellmedGateway\Jobs\LIS\RequestLabToLISJob;
use Projects\WellmedGateway\Jobs\SatuSehat\ObservationJob;
use Projects\WellmedGateway\Jobs\SatuSehat\SendSatuSehatJob;

class VisitExaminationController extends EnvironmentController
{
    public function index(ViewRequest $request){
        return $this->getVisitExaminationPaginate();
    }

    public function show(ShowRequest $request){        
        return $this->showVisitExamination();
    }

    public function store(StoreRequest $request){
        $this->userAttempt();
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
