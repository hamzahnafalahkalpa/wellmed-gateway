<?php

namespace Projects\WellmedGateway\Controllers\API\Setting;

use Hanafalah\ModuleLabRadiology\Contracts\Schemas\AnatomicalPathology;
use Projects\WellmedGateway\Controllers\API\ApiController;
use Projects\WellmedGateway\Requests\API\Setting\AnatomicalPathology\{
    ViewRequest, StoreRequest, DeleteRequest, ShowRequest
};

class AnatomicalPathologyController extends ApiController{
    public function __construct(
        protected AnatomicalPathology $__schema
    ){
        parent::__construct();
    }

    public function index(ViewRequest $request){
        return $this->__schema->viewAnatomicalPathologyList();
    }

    public function show(ShowRequest $request){
        return $this->__schema->showAnatomicalPathology();
    }

    public function store(StoreRequest $request){
        $treatment = request()->treatment;
        foreach ($treatment['service_prices'] as &$service_price) {
            $service_price['service_item_type'] ??= 'TariffComponent';
        }

        $lab_samples = request()->lab_samples ?? [];
        foreach ($lab_samples as $lab_sample) {
            $lab_sample['model_type'] = 'AnatomicalPathology';
            $lab_sample['relation_type'] = 'Sample';
        }

        request()->merge([
            'treatment' => $treatment,
            'lab_samples' => $lab_samples
        ]);
        return $this->__schema->storeAnatomicalPathology();
    }

    public function destroy(DeleteRequest $request){
        return $this->__schema->deleteAnatomicalPathology();
    }
}