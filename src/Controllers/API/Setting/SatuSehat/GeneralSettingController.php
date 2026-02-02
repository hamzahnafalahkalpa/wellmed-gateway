<?php

namespace Projects\WellmedGateway\Controllers\API\Setting\SatuSehat;

use Projects\WellmedBackbone\Contracts\Schemas\ModuleWarehouse\Room;
use Projects\WellmedBackbone\Contracts\Schemas\ModuleWorkspace\Workspace;
use Projects\WellmedBackbone\Contracts\Schemas\SatuSehat\GeneralSetting;
use Projects\WellmedGateway\Controllers\API\ApiController;
use Projects\WellmedGateway\Requests\API\Setting\SatuSehat\GeneralSetting\{
    ViewRequest, ShowRequest, StoreRequest, DeleteRequest
};

class GeneralSettingController extends ApiController{
    public function __construct(
        protected GeneralSetting $__schema,
        protected Room $__room,
        protected Workspace $__workspace,
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
                'ihs_number' => $satu_sehat['ihs_number'] ?? null,
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
        $datas['organization'] ??= null;
        $datas['locations'] ??= null;
        $datas['practitioners'] ??= null;
        $organization  = &$datas['organization'];
        $locations     = &$datas['locations'];
        $practitioners = &$datas['practitioners'];
        $workspace = $this->getWorkspace();
        if (isset($organization)) {
            $integration = $workspace->integration;
            if (!isset($integration)) $integration = $workspace->getIntegrationPayload();
            $satu_sehat = &$integration['satu_sehat']['general'];
            $satu_sehat['ihs_number'] = $organization['organization_id'];
            $satu_sehat['client_id'] = $organization['client_id'];
            $satu_sehat['client_secret'] = $organization['client_secret'];
            $workspace->setAttribute('integration',$integration);
            $workspace->save();
            if (!isset($satu_sehat['ihs_number'])){
                try {
                    $this->__workspace->prepareStoreSatuSehatOrganization($workspace);
                } catch (\Throwable $th) {
                    throw $th;
                }
            }
            $organization = [
                'organization_id' => $satu_sehat['ihs_number'],
                'client_id' => $satu_sehat['client_id'],
                'client_secret' => $satu_sehat['client_secret']
            ];
        }
        $workspace->refresh();
        $integration = $workspace->integration;
        if (isset($integration) && isset($integration['satu_sehat']['general']['ihs_number'])){
            if (isset($locations)) {
                foreach ($locations as &$location) {
                    $location = [
                        "id" => $location['id'] ?? null,
                        'name' => 'GeneralSettingLocation',
                        'reference_type' => 'Room',
                        'reference_id' => $location['reference_id'],
                        'method' => 'GET',
                        'ihs_number' => $location['ihs_number'] ?? null
                    ];
                    $room = $this->RoomModel()->findOrFail($location['reference_id']);
                    $room->ihs_number = $location['ihs_number'];
                    $room->save();
                    if (!isset($room->ihs_number)){
                        try {
                            $this->__room->prepareStoreSatuSehatLocation(
                                $this->requestDTO(config('app.contracts.RoomData'),[
                                    'id' => $room->id,
                                    'name' => $room->name,
                                    'building_id' => $room->building_id,
                                    'room_model' => $room
                                ])
                            ,$room);
                        } catch (\Throwable $th) {
                            throw $th;
                        }
                    }
                }
            }
            if (isset($practitioners)) {
                foreach ($practitioners as &$practitioner) {
                    $practitioner = [
                        "id" => $practitioner['id'] ?? null,
                        'name' => 'GeneralSettingPractitioner',
                        'reference_type' => 'Employee',
                        'reference_id' => $practitioner['reference_id'],
                        'method' => 'GET',
                        'ihs_number' => $practitioner['ihs_number'] ?? null,
                        'env_type' => config('satu-sehat.environment.env_type'),
                    ];
                    $employee = $this->EmployeeModel()->findOrFail($practitioner['reference_id']);
                    $card_identity = $employee->card_identity;
                    $card_identity['ihs_number'] = $practitioner['ihs_number'];
                    $employee->setAttribute('card_identity',$card_identity);
                    $employee->save();
                }
            }
        }
        // request()->replace($datas);
        // $collections = $this->__schema->storeMultipleGeneralSetting($datas);
        return $datas;
    }

    public function destroy(DeleteRequest $request){
        return $this->__schema->deleteGeneralSetting();
    }

    private function getWorkspace(){
        $tenant = tenancy()->tenant;
        return $tenant->reference;
    }
}