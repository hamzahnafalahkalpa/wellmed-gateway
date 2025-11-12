<?php

namespace Projects\WellmedGateway\Controllers\API\Setting;

use Projects\WellmedGateway\Controllers\API\ApiController;
use Hanafalah\ModuleMedicalTreatment\Contracts\Schemas\MedicalTreatment as SchemasMedicalTreatment;
use Projects\WellmedGateway\Requests\API\Setting\MedicalTreatment\{
    ViewRequest, StoreRequest, DeleteRequest, ShowRequest
};
class MedicalTreatmentController extends ApiController{
    public function __construct(
        protected SchemasMedicalTreatment $__schema
    ){
        parent::__construct();
    }

    public function index(ViewRequest  $request){
        return $this->__schema->viewMedicalTreatmentPaginate();
    }

    public function store(StoreRequest $request){
        $treatment = request()->treatment;
        foreach ($treatment['service_prices'] as &$service_price) {
            $service_price['service_item_type'] ??= 'TariffComponent';
        }
        request()->merge(['treatment' => $treatment]);
        return $this->__schema->storeMedicalTreatment();
    }

    public function destroy(DeleteRequest $request){
        return $this->__schema->deleteMedicalTreatment();
    }

    public function show(ShowRequest $request){
        return $this->__schema->showMedicalTreatment();
    }
}
