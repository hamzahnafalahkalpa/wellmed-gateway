<?php

namespace Projects\WellmedGateway\Controllers\API\PharmacyDepartment\Dispense;

use Projects\WellmedGateway\Requests\API\PharmacyDepartment\Dispense\{
    ViewRequest, StoreRequest, ShowRequest, DeleteRequest
};
use Projects\WellmedGateway\Controllers\API\PatientEmr\VisitRegistration\EnvironmentController;

class DispenseController extends EnvironmentController
{
    protected function commonRequest(){
        request()->merge([
            'search_medic_service_label' => 'INSTALASI FARMASI',
            'search_visit_patient_reference_type' => 'Patient'
        ]);
    }

    public function index(ViewRequest $request){
        return $this->getVisitRegistrationPaginate();
    }

    public function store(StoreRequest $request){
        $medic_service = $this->MedicServiceModel();
        $medic_service = (!isset(request()->medic_service_id))
            ? $medic_service->where('label','INSTALASI FARMASI')->firstOrFail()
            : $medic_service->findOrFail(request()->medic_service_id);

        request()->merge([
            'medic_service_id' => $medic_service->getKey(),
        ]);
        return $this->storeVisitRegistration();
    }

    public function show(ShowRequest $request){
        return $this->showVisitRegistration();
    }

    public function destroy(DeleteRequest $request){
        return $this->deleteVisitRegistration();
    }
}
