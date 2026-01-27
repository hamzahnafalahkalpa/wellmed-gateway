<?php

namespace Projects\WellmedGateway\Controllers\API\PatientEmr\Reservation;

use Projects\WellmedGateway\Requests\API\PatientEmr\Reservation\{
    ViewRequest, ShowRequest, StoreRequest, DeleteRequest
};

class ReservationController extends EnvironmentController
{ 
    public function commonRequest(){

    }

    public function index(ViewRequest $request){
        return $this->getReservationPaginate();
    }

    public function show(ShowRequest $request){
        return $this->showReservation();
    }

    public function store(StoreRequest $request){
        return $this->storeReservation();
    }

    public function destroy(DeleteRequest $request){
        return $this->deleteReservation();
    }
}