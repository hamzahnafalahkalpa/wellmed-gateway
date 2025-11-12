<?php

namespace Projects\WellmedGateway\Controllers\API\PharmacyDepartment\Frontline;

use Projects\WellmedGateway\Requests\API\PharmacyDepartment\Frontline\{
    ViewRequest, ShowRequest
};
use Projects\WellmedGateway\Controllers\API\PatientEmr\VisitExamination\EnvironmentController;

class FrontlineController extends EnvironmentController
{
    public function commonConditional($query){
        $query->whereHas('visitPatient',function($query){
            $query->flagIn('VisitPatient');
        })->where('props->is_has_prescription',true);
        // ->whereHas('assessment',function($query){
        //     $query->whereIn('morph',['BasicPrescription','MedicinePrescription','MedicToolPrescription','MixMedicinePrescription']);
        // });
    }

    public function index(ViewRequest $request){
        return $this->getVisitExaminationPaginate();
    }

    public function show(ShowRequest $request){
        return $this->showVisitExamination();
    }
}
