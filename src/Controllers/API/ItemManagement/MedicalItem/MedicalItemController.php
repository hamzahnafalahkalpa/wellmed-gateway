<?php

namespace Projects\WellmedGateway\Controllers\API\ItemManagement\MedicalItem;

use Hanafalah\ApiHelper\Requests\Token\ShowRequest;
use Hanafalah\ModuleMedicalItem\Contracts\Schemas\MedicalItem;
use Projects\WellmedGateway\Controllers\API\ApiController;
use Projects\WellmedGateway\Requests\API\ItemManagement\MedicalItem\{
    DeleteRequest, StoreRequest, ViewRequest
};

class MedicalItemController extends ApiController{
    public function __construct(
        protected MedicalItem $__schema    
    ){
        parent::__construct();
    }

    public function index(ViewRequest $request) {
        $flag = request()->flag ?? [
            $this->MedicineModelMorph(),
            $this->MedicToolModelMorph()
        ];
        request()->merge([
            'flag' => $flag
        ]);
        return $this->__schema->viewMedicalItemPaginate();
    }

    public function store(StoreRequest $request){
        $reference = request()->medicine ?? request()->medic_tool ?? request()->reagent;
        request()->merge([
            'reference'   => $reference,
            'medicine'    => null,
            'medic_tool'  => null,
            'reagent'     => null,
        ]);
        return $this->__schema->storeMedicalItem();
    }

    public function show(ShowRequest $request){
        return $this->__schema->showMedicalItem();
    }

    public function destroy(DeleteRequest $request){
        return $this->__schema->deleteMedicalItem();
    }
}
