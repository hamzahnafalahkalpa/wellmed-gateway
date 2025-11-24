<?php

namespace Projects\WellmedGateway\Controllers\API\PatientEmr\VisitExamination;

use Projects\WellmedGateway\Requests\API\PatientEmr\VisitExamination\{
    ViewRequest, ShowRequest, StoreRequest, DeleteRequest
};
use Illuminate\Support\Facades\Hash;
use Projects\WellmedGateway\Jobs\LIS\RequestLabToLISJob;
use Projects\WellmedGateway\Jobs\SatuSehat\ObservationJob;
use Projects\WellmedGateway\Jobs\SatuSehat\SendSatuSehatJob;

class VisitExaminationController extends EnvironmentController
{
    public function index(ViewRequest $request){
        return $this->getVisitExaminationPaginate();
    }

    public function show(ShowRequest $request){
        return $this->showVisitExamination();
    }

    public function store(StoreRequest $request){
        return $this->storeVisitExamination();
    }

    public function destroy(DeleteRequest $request){
        return $this->deleteVisitExamination();
    }
}
