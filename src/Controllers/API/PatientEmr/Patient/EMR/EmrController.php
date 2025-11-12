<?php

namespace Projects\WellmedGateway\Controllers\API\PatientEmr\Patient\EMR;

use Barryvdh\DomPDF\Facade\Pdf;
use Gii\ModuleExamination\Contracts\Examination\Assessment\Assessment;
use Illuminate\Http\Request;
use Projects\WellmedGateway\Requests\API\PatientEmr\Patient\EMR\{
    ShowRequest,ViewRequest
};
use Illuminate\Support\Str;
use Projects\WellmedGateway\Controllers\API\ApiController as ApiBaseController;

class EmrController extends ApiBaseController {

    public function __construct(
      protected Assessment $__schema_assesment
    ){}

    public function show(ShowRequest $request){
       request()->merge([
           'flag' => Str::studly(request()->flag)
       ]);

       return $this->__schema_assesment->showPatientEmrByFlag();
    }


    public function export(Request $request) {
        if(isset(request()->medic_service_id)){
            // if RADIOLOGY
            $visit_patient_id = request()->visit_patient_id;
            $radiology = $this->MedicServiceModel()->where('flag','RADIOLOGY')->where("id",request()->medic_service_id)->first();
            $laboratory = $this->MedicServiceModel()->where('flag','LABORATORY')->where("id",request()->medic_service_id)->first();
            if(isset($radiology)){
                $visit_registrations_id = $this->VisitRegistrationModel()->where('visit_patient_id', $visit_patient_id)->get()->pluck('id');
                $visit_examination_ids = $this->VisitExaminationModel()->whereIn('visit_registration_id', $visit_registrations_id)->get()->pluck('id');

                $results['radiology'] = $this->AssessmentModel()
                ->whereIn("visit_examination_id", $visit_examination_ids)
                ->where("morph", "RadiologyTreatment")
                ->get();
                $views = [];

                $profile = $this->getProfileData($visit_patient_id);
                $results['profile'] = $profile->patient;
                $results['visit'] = $profile;

                $workspace = tenancy()->tenant->reference;
                foreach ($results['radiology'] as $key => $result) {
                    $employee_id = $workspace->setting['radiology']['head_doctor']['id'] ?? null;
                    $radiologist = null;
                    if ($employee_id) {
                        $radiologist = $this->EmployeeModel()->find($employee_id);
                    }
                    $data = [
                        "profile"      => $results['profile'],
                        "radiology"    => $result,
                        "visit"        => $results['visit'],
                        "employee"     => ($this->global_employee) ? $this->global_employee->prop_people['name'] : "",
                        "practitioner" => $this->PractitionerEvaluationModel()->where('visit_examination_id', $result['visit_examination_id'])->first(),
                        "digitalSign"  => ($radiologist) ? $radiologist->digital_sign : "",
                    ];

                    $views[] = view('exports.radiology-result', ["results" => $data])->render();

                    if (isset($result) && isset($result->paths)) {
                        foreach ($result->paths as $key => $value) {
                            $data['image'] = $value;
                            $views[] = view('exports.radiology-result-image', ["results" => $data])->render();
                        }
                    }
                }

                $html = implode('', $views);
                return $this->streamPdf($html);
            }
            if (isset($laboratory)) {
                $medicServiceLab = $this->MedicServiceModel()->where('flag', 'LABORATORY')->whereNull('parent_id')->first()->id;
                $visitRegistration = $this->VisitRegistrationModel()->where('medic_service_id', $medicServiceLab)->where('visit_patient_id', $visit_patient_id)->first();
                $visitExamination = $this->VisitExaminationModel()->where('visit_registration_id', $visitRegistration->id)->first();
                if($visitExamination){
                    return response()->streamDownload(function() use ($visitExamination) {
                        echo file_get_contents($visitExamination->result_pdf);
                    }, 'lab-result.pdf', [
                        'Content-Type' => 'application/pdf'
                    ]);
                }
            }
        }

        $model = $this->VisitPatientModel()->where("id",request()->visit_patient_id)->with("patient","visitRegistration.visitExamination")->first();
        if(isset($model)) {
            $results['profile']  = $model->patient;
                if(isset($model->visitRegistration)) {
                    $model = $model->visitRegistration->visitExamination;
                }
        }else {
            $model   = $this->VisitExaminationModel()->with("visitRegistration.visitPatient.patient")
                ->where("id", request()->visit_patient_id ?? request()->visit_examination_id)
                ->first();
            $results['profile']   = $model;
            $results['profile']   = $results['profile']->visitRegistration->visitPatient->patient;
        }


        $assesments = $this->AssessmentModel()->where("visit_examination_id", $model->id)->get();
        foreach ($assesments as $assesment) {
            $results[$assesment->morph] = $assesment;
        }

        $results['registration'] = $results['profile']->visitRegistration;

        $diagnoses = [];
        foreach (['InitialDiagnose', 'PrimaryDiagnose', 'SecondaryDiagnose'] as $key) {
            if (!empty($results[$key])) {
                $diagnoses[] = $results[$key];
                $isDiagnose  = true;
            } else $isDiagnose  = false;
        }

        if($isDiagnose) $results['Diagnose'] = $diagnoses;


        return Pdf::loadView(
            "padma-surabaya-views.EMR.export.export-emr",
            ['datas' => $results]
        )->setPaper('A4')->stream();
    }

    private function streamPdf(string $html)
    {
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultPaperSize', 'A4');
        $options->set('isPhpEnabled', true);

        $pdf = new \Dompdf\Dompdf($options);
        $pdf->loadHtml($html);
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();

        return $pdf->stream();
    }

    private function getProfileData($visit_patient_id)
    {
        return $this->VisitPatientModel()->with(['patient.reference' => function ($q) {
            $q->with([
                'country',
                'addresses' => fn($query) => $query->whereIn('flag', ['RESIDENCE', 'ID_CARD'])
            ]);
        }])->find($visit_patient_id);
    }
}
