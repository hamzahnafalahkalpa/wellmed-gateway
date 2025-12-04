<?php

namespace Projects\WellmedGateway\Controllers\API\Setting\SatuSehat;

use Projects\WellmedBackbone\Contracts\Schemas\SatuSehat\GeneralSetting;
use Projects\WellmedGateway\Controllers\API\ApiController;
use Projects\WellmedGateway\Requests\API\Setting\SatuSehat\GeneralSetting\{
    ViewRequest, ShowRequest, StoreRequest, DeleteRequest
};

class GeneralSettingController extends ApiController{
    public function __construct(
        protected GeneralSetting $__schema
    ){
        parent::__construct();
    }

    public function index(ViewRequest $request){
        $workspace = $this->getWorkspace();
        $integration = $workspace->integration;
        $satu_sehat = $integration['satu_sehat']['general'] ?? [];
        return [
            'organization' => [
                'organization_id' => $satu_sehat['organization_id'] ?? null,
                'client_id' => $satu_sehat['client_id'] ?? null,
                'client_secret' => $satu_sehat['client_secret'] ?? null,
            ],
            'locations' => call_user_func(function(){
                $rooms = $this->RoomModel()->get();
                $room_datas = [];
                foreach ($rooms as $room) {
                    $room_datas[] = [
                        'id' => null,
                        'reference_type' => 'Room',
                        'reference_id' => $room->id,
                        'name' => $room->name,
                        'ihs_number' => $room->ihs_number,
                    ];
                }
                return $room_datas;
            }),
            'practitioners' => call_user_func(function(){
                $employees = $this->EmployeeModel()->get();
                $employee_datas = [];
                foreach ($employees as $employee) {
                    $card_identity = $employee->card_identity;
                    $employee_datas[] = [
                        'id' => null,
                        'reference_type' => 'Employee',
                        'reference_id' => $employee->id,
                        'name' => $employee->name,
                        'ihs_number' => $card_identity['ihs_number'] ?? null,
                    ];
                }
                return $employee_datas;
            }),
        ];
    }

    public function show(ShowRequest $request){
        return $this->__schema->showGeneralSetting();
    }

    public function store(StoreRequest $request){
        $datas = request()->all();
        $organization  = $datas['organization'] ?? null;
        $locations     = $datas['locations'] ?? null;
        $practitioners = $datas['practitioners'] ?? null;
        $datas = [];
        if (isset($organization)) {
            $workspace = $this->getWorkspace();
            $integration = $workspace->integration;
            $satu_sehat = &$integration['satu_sehat']['general'];
            $satu_sehat['organization_id'] = $organization['organization_id'];
            $satu_sehat['client_id'] = $organization['client_id'];
            $satu_sehat['client_secret'] = $organization['client_secret'];
            $workspace->setAttribute('integration',$integration);
            $workspace->save();
        }

        if (isset($locations)) {
            foreach ($locations as $location) {
                $datas[] = [
                    "id" => $location['id'] ?? null,
                    'name' => 'GeneralSettingLocation',
                    'reference_type' => 'Room',
                    'reference_id' => $location['reference_id'],
                    'method' => 'GET'
                ];
                $room = $this->RoomModel()->findOrFail($location['reference_id']);
                $room->ihs_number = $location['ihs_number'];
                $room->save();
            }
        }
        if (isset($practitioners)) {
            foreach ($practitioners as $practitioner) {
                $datas[] = [
                    "id" => $practitioner['id'] ?? null,
                    'name' => 'GeneralSettingPractitioner',
                    'reference_type' => 'Employee',
                    'reference_id' => $practitioner['reference_id'],
                    'method' => 'GET',
                    'env_type' => config('satu-sehat.environment.env_type'),
                ];
                $employee = $this->EmployeeModel()->findOrFail($practitioner['reference_id']);
                $card_identity = $employee->card_identity;
                $card_identity['ihs_number'] = $practitioner['ihs_number'];
                $employee->setAttribute('card_identity',$card_identity);
                $employee->save();
            }
        }
        request()->replace($datas);
        $collections = $this->__schema->storeMultipleGeneralSetting($datas);
        return $collections;
    }

    public function destroy(DeleteRequest $request){
        return $this->__schema->deleteGeneralSetting();
    }

    private function getWorkspace(){
        $tenant = tenancy()->tenant;
        return $tenant->reference;
    }
}