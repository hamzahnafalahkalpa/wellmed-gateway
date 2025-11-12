<?php

namespace Projects\WellmedGateway\Controllers\API\ItemManagement\Inventory;

use Hanafalah\ModuleItem\Contracts\Schemas\Inventory;
use Projects\WellmedGateway\Controllers\API\ApiController;
use Projects\WellmedGateway\Requests\API\ItemManagement\Inventory\{
    ViewRequest, ShowRequest, StoreRequest, DeleteRequest
};

class InventoryController extends ApiController{
    public function __construct(
        protected Inventory $__schema
    ){
        parent::__construct();
    }

    public function index(ViewRequest $request){
        return $this->__schema->viewInventoryPaginate();
    }

    public function show(ShowRequest $request){
        return $this->__schema->showInventory();
    }

    public function store(StoreRequest $request){
        $possibleTypes = ['office_supply', 'stuff_supply', 'healthcare_equipment'];

        $reference = null;
        $referenceType = null;

        foreach ($possibleTypes as $type) {
            if (request()->filled($type)) {
                $reference = request()->input($type);
                $referenceType = $type;
                break;
            }
        }

        $data = array_fill_keys($possibleTypes, null);
        $data['reference'] = $reference;
        $data['reference_type'] = $referenceType;

        request()->merge($data);

        return $this->__schema->storeInventory();
    }

    public function destroy(DeleteRequest $request){
        return $this->__schema->deleteInventory();
    }
}