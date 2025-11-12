<?php

namespace Projects\WellmedGateway\Controllers\API\PatientEmr\VisitExamination\Examination;

use Projects\WellmedGateway\Controllers\API\PatientEmr\EnvironmentController as EnvEnvironmentController;

class EnvironmentController extends EnvEnvironmentController
{
    protected function commonRequest(){
        $this->userAttempt();
        $examination = request()->examination;
        $examination['practitioner_id'] = $this->global_employee->getKey() ?? null;
        request()->merge([
            'examination' => $examination
        ]);
    }

    protected function storeExamination(){
        $room = $this->global_employee->room;
        if (isset($room)){
            $room_id = $room->getKey();
            if ($room->medic_service_name == "Instalasi Farmasi"){
                $pharmacy_id = $room->getKey();
            }else{
                if (isset($room->pharmacy)){
                    $pharmacy_id = $room->pharmacy->getKey();
                }
            }
        }
        $examination = request()->examination;
        $examination['warehouse_type'] = config('module-examination.warehouse') ?? 'Room';
        $examination['warehouse_id'] = $room_id ?? null;
        $examination['pharmacy_id']  = $pharmacy_id ?? null;
        $examination['pharmacy_type'] = config('module-examination.warehouse') ?? 'Room';
        request()->merge(['examination' => $examination]);
        return $this->__visit_examination_schema->storeVisitExamination();
    }
}
