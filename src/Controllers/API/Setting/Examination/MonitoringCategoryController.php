<?php

namespace Projects\WellmedGateway\Controllers\API\Setting\Examination;

use Hanafalah\ModuleMonitoring\Contracts\Schemas\MonitoringCategory;
use Projects\WellmedGateway\Controllers\API\ApiController;
use Projects\WellmedGateway\Requests\API\Setting\Examination\MonitoringCategory\{
    ViewRequest, StoreRequest, DeleteRequest
};

class MonitoringCategoryController extends ApiController{
    public function __construct(
        // protected MonitoringCategory $__schema
    ){}

    public function index(ViewRequest $request){
        return $this->__schema->viewMonitoringCategoryList();
    }

    public function store(StoreRequest $request){
        return $this->__schema->storeMonitoringCategory();
    }

    public function destroy(DeleteRequest $request){
        return $this->__schema->deleteMonitoringCategory();
    }
}
