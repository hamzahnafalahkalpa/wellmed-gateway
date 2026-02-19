<?php

namespace Projects\WellmedGateway\Controllers\API\PatientEmr;

use Hanafalah\ModuleAppointment\Contracts\Schemas\Reservation;
use Hanafalah\ModuleExamination\Contracts\Schemas\Examination;
use Hanafalah\ModuleExamination\Contracts\Schemas\Examination\Assessment\Assessment;
use Hanafalah\ModulePatient\Contracts\Schemas\{
    OldVisit,
    Referral
};
use Projects\WellmedBackbone\Contracts\Schemas\ModulePatient\Patient;
use Hanafalah\ModulePharmacy\Contracts\Schemas\PharmacySale;
use Hanafalah\ModuleSupport\Contracts\Schemas\Support;
use Projects\WellmedBackbone\Contracts\Schemas\ModulePatient\{
    PractitionerEvaluation,
};
use Projects\WellmedGateway\Controllers\API\ApiController as ApiBaseController;
use Projects\WellmedBackbone\Schemas\ModulePatient\VisitExamination;
use Projects\WellmedBackbone\Schemas\ModulePatient\VisitPatient;
use Projects\WellmedBackbone\Schemas\ModulePatient\VisitRegistration;

class EnvironmentController extends ApiBaseController{

    public function __construct(
        protected OldVisit $__old_visit_schema,
        protected Patient $__patient_schema,
        protected VisitExamination $__visit_examination_schema,
        protected VisitPatient $__visit_patient_schema,
        protected VisitRegistration $__visit_registration_schema,
        protected Examination $__examination_schema,
        protected PractitionerEvaluation $__practitioner_evaluation_schema,
        protected Referral $__referral_schema,
        protected Assessment $__assessment_schema,
        protected PharmacySale $__pharmacy_sale_schema,
        protected Support $__support_schema,
        protected Reservation $__reservation_schema
    )
    {
        parent::__construct();
    }

    protected function commonConditional($query){

    }

    protected function commonRequest(){
        $this->userAttempt();
    }

    protected function isEmployee(): bool{
        return isset($this->global_employee);
    }

    protected function employeeHasProfession(string $profession):bool{
        return $this->isEmployee() && isset($this->global_employee->profession) && $this->global_employee->profession['label'] == $profession;
    }

    protected function isDoctor(){
        return $this->employeeHasProfession('Doctor');
    }

    protected function isPerawat(){
        return $this->employeeHasProfession('Nurse');
    }

    protected function isMidwife(){
        return $this->employeeHasProfession('Midwife');
    }

    protected function getMedicServiceFromEmployee(){
        if (isset($this->global_employee)){
            $profession = $this->global_employee->profession;
            if (isset($profession) && $profession->label == 'Nurse'){
                $rooms = $this->global_employee->rooms;
                $medic_service_id = [];
                foreach ($rooms as $room) {
                    if (isset($room)){
                        $model_has_service = $room->modelHasService()->first();
                        if (isset($model_has_service)) $medic_service_id[] = $model_has_service->service_id;
                    }
                }
            }else{
                $room = $this->global_employee->room;
                if (isset($room)){
                    $model_has_service = $room->modelHasService()->first();
                    if (isset($model_has_service)) $medic_service_id = $model_has_service->service_id;
                }
            }
        }
        return $medic_service_id ?? null;
    }
}
