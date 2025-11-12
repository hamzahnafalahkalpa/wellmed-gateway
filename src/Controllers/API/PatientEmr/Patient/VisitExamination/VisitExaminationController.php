<?php

namespace Projects\WellmedGateway\Controllers\API\PatientEmr\Patient\VisitExamination;

use Projects\WellmedGateway\Requests\API\PatientEmr\Patient\VisitExamination\{
    StoreRequest
};
// use Projects\WellmedGateway\Controllers\API\PatientEmr\VisitExamination\EnvironmentController;
use Projects\WellmedGateway\Controllers\API\PatientEmr\VisitPatient\EnvironmentController;

class VisitExaminationController extends EnvironmentController
{
    public function store(StoreRequest $request){
        $this->commonRequest();
        $visit_examination = request()->all();
        $patient_type_service_id = $visit_examination['patient_type_service_id'] ?? $this->PatientTypeServiceModel()->where('label','UMUM')->firstOrFail()->getKey();
        $medic_service_id        = $visit_examination['medic_service_id'] ?? $this->MedicServiceModel()->where('label','UMUM')->firstOrFail()->getKey();
        $visit_patient = [
            'id' => null,
            'patient_id' => request()->patient_id,
            "patient_type_service_id" => $patient_type_service_id,
            'visit_registration' => [
                'id' => null,
                'status' => 'PROCESSING',
                "practitioner_evaluation" => [ //nullable, FOR HEAD DOCTOR
                    "practitioner_type" => "Employee", //nullable, default from config
                    "practitioner_id"=> $this->global_employee->getKey(), //GET FROM AUTOLIST - EMPLOYEE LIST (DOCTOR)
                    "as_pic"=> true //nullable, default false, in:true/false
                ],
                "medic_service_id"  => $medic_service_id,
                'visit_examination' => $visit_examination
            ]
        ];
        request()->replace($visit_patient);
        $visit_patient = $this->storeVisitPatient();
        return $this->__visit_examination_schema->showVisitExamination($this->VisitExaminationModel()->findOrFail($visit_patient['visit_registration']['visit_examination']['id']));
    }
}
