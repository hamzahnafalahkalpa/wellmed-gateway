<?php

namespace Projects\WellmedGateway\Controllers\API\Setting;

use Hanafalah\ModuleExamination\Contracts\Schemas\Form\Form;
use Projects\WellmedGateway\Controllers\API\ApiController;
use Illuminate\Http\Request;

class FormController extends ApiController{
    public function __construct(
        protected Form $__schema
    ){}

    public function index(Request $request) {
        return $this->__schema->viewFormList();
    }
}
