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
        return $this->__schema->viewGeneralSettingList();
    }

    public function show(ShowRequest $request){
        return $this->__schema->showGeneralSetting();
    }

    public function store(StoreRequest $request){
        $datas = request()->all();
        $organization  = $datas['organization'];
        $locations     = $datas['locations'];
        $practitioners = $datas['practitioners'];
        $datas = [];
        if (isset($organization)) {
            $tenant = tenancy()->tenant;
            $workspace = $tenant->reference;
            $integration = $workspace->integration;
            $satu_sehat = &$integration['satu_sehat']['general'];
            $satu_sehat['organization_id'] = $organization['organization_id'];
            $satu_sehat['client_id'] = $organization['client_id'];
            $satu_sehat['client_secret'] = $organization['client_secret'];
            $workspace->setAttribute('integration',$integration);
            $workspace->save();
        }

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
        request()->replace($datas);
        $collections = $this->__schema->storeMultipleGeneralSetting($datas);
        return $collections;
    }

    public function destroy(DeleteRequest $request){
        return $this->__schema->deleteGeneralSetting();
    }
}