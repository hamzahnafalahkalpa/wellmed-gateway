<?php

namespace Projects\WellmedGateway\Controllers\API\Setting;

use Hanafalah\ModuleEncoding\Contracts\Schemas\Encoding;
use Projects\WellmedGateway\Controllers\API\ApiController;
use Projects\WellmedGateway\Requests\API\Setting\Encoding\{
    ViewRequest, ShowRequest, StoreRequest, DeleteRequest
};

class EncodingController extends ApiController{
    public function __construct(
        protected Encoding $__encoding_schema
    ){}

    private function mapper(){
        $this->userAttempt();
        request()->merge([
            'reference_id'   => $this->global_workspace->getKey(),
            'reference_type' => $this->global_workspace->getMorphClass()
        ]);
    }
    
    public function index(ViewRequest $request){
        $this->mapper();
        return $this->__encoding_schema->viewEncodingList();
    }

    public function show(ShowRequest $request) {
        $this->mapper();
        return $this->__encoding_schema->showEncoding();
    }

    public function store(StoreRequest $request){
        $this->mapper();
        $model_has_encoding = request()->model_has_encoding;
        $model_has_encoding['reference_id']   = $this->global_workspace->getKey();
        $model_has_encoding['reference_type'] = $this->global_workspace->getMorphClass();
        request()->merge([
            'model_has_encoding' => $model_has_encoding
        ]);
        return $this->__encoding_schema->storeEncoding();
    }

    public function destroy(DeleteRequest $request){
        $this->mapper();
        return $this->__encoding_schema->deleteEncoding();
    }
}
