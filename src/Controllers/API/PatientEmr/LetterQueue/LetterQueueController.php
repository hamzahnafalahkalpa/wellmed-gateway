<?php

namespace Projects\WellmedGateway\Controllers\API\PatientEmr\LetterQueue;

use Projects\WellmedGateway\Requests\API\PatientEmr\LetterQueue\{
    ShowRequest, ViewRequest
};

class LetterQueueController extends EnvironmentController{

    public function index(ViewRequest $request){
        return $this->getVisitRegistrationPaginate();
    }

    public function show(ShowRequest $request){
        return $this->showVisitRegistration();
    }
}
