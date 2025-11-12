<?php

namespace Projects\WellmedGateway\Controllers\API\Navigation\Notification;

use Hanafalah\ModuleEmployee\Contracts\Schemas\Employee;
use Projects\WellmedGateway\Controllers\API\ApiController;
use Projects\WellmedGateway\Requests\API\Navigation\Profile\ShowRequest;

class NotificationController extends ApiController{
    public function __construct(
        protected Employee $__employee_schema    
    ){}


    public function show(ShowRequest $request){
        return $this->__employee_schema->showProfile();
    }
}