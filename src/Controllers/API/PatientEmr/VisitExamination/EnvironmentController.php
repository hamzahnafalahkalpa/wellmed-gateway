<?php

namespace Projects\WellmedGateway\Controllers\API\PatientEmr\VisitExamination;

use Projects\WellmedGateway\Controllers\API\PatientEmr\EnvironmentController as EnvEnvironmentController;

class EnvironmentController extends EnvEnvironmentController
{
    protected function commonRequest(){
        $this->userAttempt();
        $examination = request()->examination;
        if (isset($examination)){
            if (isset($this->global_employee)){
                $examination['practitioner_evaluations'][] = [
                    'practitioner_type' => $this->global_employee->getMorphClass(),
                    'practitioner_id' => $this->global_employee->getKey() ?? null
                ];
            }
            request()->merge([
                'examination' => $examination
            ]);
        }
    }

    protected function storeExamination(){
        $this->commonRequest();
        return $this->__visit_examination_schema->storeVisitExamination();
    }

    protected function getVisitExaminationPaginate(?callable $callback = null){        
      $this->commonRequest();
      return $this->__visit_examination_schema->conditionals(function($query) use ($callback){
          $this->commonConditional($query);
          $query->when(isset($callback),function ($query) use ($callback){
              $callback($query);
          });
      })->viewVisitExaminationPaginate();
    }

    protected function showVisitExamination(?callable $callback = null){        
        $this->commonRequest();
        return $this->__visit_examination_schema->conditionals(function($query) use ($callback){
            $this->commonConditional($query);
            $query->when(isset($callback),function ($query) use ($callback){
                $callback($query);
            });
        })->showVisitExamination();
    }

    protected function deleteVisitExamination(?callable $callback = null){        
        $this->commonRequest();
        return $this->__visit_examination_schema->conditionals(function($query) use ($callback){
            $this->commonConditional($query);
            $callback($query);
        })->deleteVisitExamination();
    }

    protected function storeVisitExamination(?callable $callback = null){
        $this->commonRequest();
        $examination = request()->examination;
        if (isset($examination)){
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
            $examination['warehouse_type'] = config('module-examination.warehouse') ?? 'Room';
            $examination['warehouse_id'] = $room_id ?? null;
            $examination['pharmacy_id']  = $pharmacy_id ?? null;
            $examination['pharmacy_type'] = config('module-examination.warehouse') ?? 'Room';
            request()->merge(['examination' => $examination]);
        }
        return $this->__visit_examination_schema->conditionals(function($query) use ($callback){
            $this->commonConditional($query);
            $callback($query);
        })->storeVisitExamination();
    }
}
