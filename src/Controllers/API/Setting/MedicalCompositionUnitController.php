<?php

namespace Projects\WellmedGateway\Controllers\API\Setting;

use Hanafalah\ModuleMedicalItem\Contracts\Schemas\MedicalCompositionUnit;
use Projects\WellmedGateway\Controllers\API\ApiController;
use Projects\WellmedGateway\Requests\API\Setting\MedicalCompositionUnit\{
    ViewRequest, StoreRequest, DeleteRequest
};

class MedicalCompositionUnitController extends ApiController{
    public function __construct(
        protected MedicalCompositionUnit $__medicalcompositionunit_schema
    ){
        parent::__construct();
    }

    public function index(ViewRequest $request){
        return $this->__medicalcompositionunit_schema->viewMedicalCompositionUnitList();
    }

    public function store(StoreRequest $request){
        return $this->__medicalcompositionunit_schema->storeMedicalCompositionUnit();
    }

    public function destroy(DeleteRequest $request){
        return $this->__medicalcompositionunit_schema->deleteMedicalCompositionUnit();
    }
}