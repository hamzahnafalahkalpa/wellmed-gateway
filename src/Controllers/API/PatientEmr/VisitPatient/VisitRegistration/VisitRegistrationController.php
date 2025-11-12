<?php

namespace Projects\WellmedGateway\Controllers\API\PatientEmr\VisitPatient\VisitRegistration;

use Projects\WellmedGateway\Controllers\API\PatientEmr\VisitRegistration\EnvironmentController;
use Projects\WellmedGateway\Requests\API\PatientEmr\VisitPatient\VisitRegistration\{
    ViewRequest
};

class VisitRegistrationController extends EnvironmentController
{
    public function index(ViewRequest $request){
        return $this->getVisitRegistrationPaginate();

        $is_perawat = false;
        if (isset($this->global_employee)){
            $profession = $this->global_employee->profession;
            if ($is_perawat = isset($profession) && $profession->name == 'Perawat'){
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

        request()->merge([
            'flag'             => 'OUTPATIENT',
            'medic_service_id' => $medic_service_id ?? null
        ]);
        
        return $this->__visit_registration_schema->conditionals(function($query) use ($is_perawat){
            $query->whereNull("props->is_mcu")
                  ->when(isset($this->global_employee) && isset($is_perawat) && !$is_perawat,function($query){
                      $query->whereNull('head_doctor_id')
                            ->orWhere(function($query){
                                $query->whereNotNull('head_doctor_id')
                                      ->where('head_doctor_id',$this->global_employee->getKey());
                            });
                  });
        })->viewVisitRegistrationPaginate();
    }
}
