<?php

namespace Projects\WellmedGateway\Concerns;

use Illuminate\Support\Str;

trait HasApproval {
    protected function approver(string $model_name){
        if (isset(request()->id)){
            $model = $this->{$model_name.'Model'}()->findOrFail(request()->id);
            $approval   = $model->approval;
        }else{
            $approval = [
                'note' => null,
                'status' => null,
                'approver' => [
                    'estimator_id' => null,
                    'coo_id' => null,
                    'cto_id' => null,
                    'ceo_id' => null,
                    'finance_id' => null
                ]
            ];
        }
        if (!isset($this->global_employee)) $this->userAttempt();
        $employee = $this->global_employee;
        if (isset($employee)){
            $occupation = $employee->prop_occupation;
            if (isset($occupation)){
                $name = Str::lower($occupation['name']);
                if (in_array($name,['Staff Finance','Divisi Finance'])) {
                    $name = 'finance';
                }
                $approval['approver'][$name.'_id'] = $employee->getKey();
                $approve = request()->approval == 'true' ? true : (request()->approval == 'false' ? false : null);
                $approval['approver'][$name] = [
                    'id'     => $employee->getKey(),
                    'name'   => $employee->name,
                    'status' => $approve,
                    'at'     => now()->format('Y-m-d H:i:s')
                ];
    
                if (!$approve){
                    request()->merge([
                        'status' => 'CANCELED'
                    ]);
                    $approval['status'] = 'CANCELED';
                    $approval['note']   = request()->note;
                }else{
                    if ($name == 'finance'){
                        request()->merge([
                            'reported_at' => now()
                        ]);
                    }
                }
            }
        }
        return $approval;
    }
}