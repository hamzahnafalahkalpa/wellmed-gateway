<?php

namespace Projects\WellmedGateway\Controllers\API\PatientEmr\VisitRegistration;

use Projects\WellmedGateway\Requests\API\PatientEmr\VisitRegistration\{
    ViewRequest, StoreRequest, ShowRequest, DeleteRequest
};
use Illuminate\Support\Str;

class VisitRegistrationController extends EnvironmentController
{
    protected function commonRequest(){
        parent::commonRequest();
        $medic_service_label = request()->search_medic_service_label ?? request()->flag ?? null;
        if (isset($medic_service_label)) {
            $medic_service_label = $this->mustArray($medic_service_label);
            foreach ($medic_service_label as $key => $label) {
                $medic_service_label[$key] = Str::upper($label);
            }
            request()->merge([
                'search_medic_service_label' => $medic_service_label,
            ]);
        }
    }

    public function index(ViewRequest $request){
        return $this->getVisitRegistrationPaginate();

    }

    public function show(ShowRequest $request){
        return $this->showVisitRegistration();
    }

    public function store(StoreRequest $request){
        return $this->storeVisitRegistration();
    }

    public function destroy(DeleteRequest $request){
        return $this->deleteVisitRegistration();
    }
}
