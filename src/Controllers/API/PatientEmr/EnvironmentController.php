<?php

namespace Projects\WellmedGateway\Controllers\API\PatientEmr;

use Hanafalah\ModuleAppointment\Contracts\Schemas\Reservation;
use Hanafalah\ModuleExamination\Contracts\Schemas\Examination;
use Hanafalah\ModuleExamination\Contracts\Schemas\Examination\Assessment\Assessment;
use Hanafalah\ModulePatient\Contracts\Schemas\{
    Referral
};
use Projects\WellmedBackbone\Contracts\Schemas\ModulePatient\Patient;
use Hanafalah\ModulePharmacy\Contracts\Schemas\PharmacySale;
use Hanafalah\ModuleSupport\Contracts\Schemas\Support;
use Projects\WellmedBackbone\Contracts\Schemas\ModulePatient\{
    PractitionerEvaluation,
};
use Projects\WellmedGateway\Controllers\API\ApiController as ApiBaseController;
use Hanafalah\LaravelSupport\Jobs\ElasticJob;
use Projects\WellmedBackbone\Schemas\ModulePatient\VisitExamination;
use Projects\WellmedBackbone\Schemas\ModulePatient\VisitPatient;
use Projects\WellmedBackbone\Schemas\ModulePatient\VisitRegistration;

class EnvironmentController extends ApiBaseController{

    public function __construct(
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

    protected function elasticForVisitPatient(string|array $visit_patient_id, bool $as_bulk_datas = false){
        if (is_array($visit_patient_id)){
            $visit_patient = $visit_patient_id; //visit_patient result show
        }else{
            $visit_patient_model = $this->VisitPatientModel();
            $shows = array_merge($visit_patient_model->showUsingRelation(),[
                'visitRegistrations.paymentSummary.paymentDetails.transactionItem',
            ]);
            $visit_patient_model = $visit_patient_model->with($shows)->findOrFail($visit_patient_id);
        }
        $visit_registration_data = [
            'index' => config('app.elasticsearch.indexes.visit_registration.full_name'),
            'data'  => []
        ];
        $visit_exam_data = [
            'index' => config('app.elasticsearch.indexes.visit_examination.full_name'),
            'data' => []
        ];
        foreach ($visit_patient_model->visitRegistrations as $visit_registration) {
            $visit_registration->load($visit_registration->showUsingRelation());
            $visit_registration_data['data'][] = json_decode(json_encode($visit_registration->toShowApi()->resolve()),true);
            if (isset($visit_registration->visitExamination)){
                $visit_examination = $visit_registration->visitExamination;
                $visit_examination->load($visit_examination->showUsingRelation());
                $visit_exam_data['data'][] = json_decode(json_encode($visit_examination->toShowApi()->resolve()),true);
            }
        }   
        $visit_patient ??= $visit_patient_model->toShowApi()->resolve();
        $visit_patient = json_decode(json_encode($visit_patient),true);
        $visit_patient_data = [
            'index' => config('app.elasticsearch.indexes.visit_patient.full_name'),
            'data'  => [
                $visit_patient
            ]
        ];
        if ($as_bulk_datas){
            return [
                $visit_patient_data,
                $visit_registration_data,
                $visit_exam_data
            ];
        }else{
            dispatch(new ElasticJob([
                'type'  => 'BULK',
                'datas' => [
                    $visit_patient_data,
                    $visit_registration_data,
                    $visit_exam_data
                ]
            ]))
            ->onQueue('elasticsearch')
            ->onConnection('rabbitmq');
        }
    }
}
