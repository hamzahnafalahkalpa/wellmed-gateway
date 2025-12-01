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
        foreach ($locations as $location) {
            $datas[] = [
                "id" => $location['id'] ?? null,
                'name' => 'GeneralSettingLocation',
                'reference_type' => 'Room',
                'reference_id' => $location['reference_id'],
                'method' => 'GET'
            ];
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
        }
        request()->replace($datas);
        $collections = $this->__schema->storeMultipleGeneralSetting($datas);
        return $collections;
    }

    public function destroy(DeleteRequest $request){
        return $this->__schema->deleteGeneralSetting();
    }
}