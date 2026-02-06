<?php

namespace Projects\WellmedGateway\Controllers\API\PatientEmr\DocumentType\Export;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DocumentTypeExportController extends EnvironmentController{
    protected function commonConditional($query){
    }

    public function show(Request $request){
        $workspace = tenancy()->tenant->reference;
        $workspace = $workspace->load($workspace->showUsingRelation());
        $workspace = $workspace->toShowApi()->resolve();
        $workspace = json_decode(json_encode($workspace));
        switch (request()->type) {
            case 'medical-certificate':
            case 'informed-consent':
                request()->merge([
                    'morph' => 'InformedConsent'
                ]);
                $assessment = $this->AssessmentModel()->findOrFail(request()->id);
                $assessment = $this->{$assessment->morph.'Model'}()->findOrFail($assessment->id);
                $assessment = $assessment->toShowApi()->resolve();
                $exam = &$assessment['exam'];
                $dynamic_forms = $exam['dynamic_forms'] ?? [];
                $forms = [];
                foreach ($dynamic_forms as $dynamic_form) {
                    if ($dynamic_form['key'] == 'treatment'){
                        foreach ($dynamic_form['value'] as $value) {
                            $treatment = $this->TreatmentModel()->findOrFail($value);
                            $forms['treatment'][] = $treatment->toShowApi()->resolve();
                        }
                    }else{
                        $forms[$dynamic_form['key']] = $dynamic_form['value'] ?? null;
                    }
                }
                $assessment['exam']['forms'] = $forms;
                $visit_examination = $this->VisitExaminationModel()->with('patient.reference.addresses','visitRegistration')->findOrFail($assessment['examination_id']);
                $patient = $visit_examination->patient;
                $patient = json_decode(json_encode($patient->toShowApi()->resolve()));
                $assessment = json_decode(json_encode($assessment));
                $document_type = $assessment->exam->document_type ?? null;
                $morph = Str::lower($document_type->label);

                $visit_examination = $visit_examination->toShowApi()->resolve();
                $visit_examination = json_decode(json_encode($visit_examination));
                $visit_examination->created_at = \Carbon\Carbon::parse($visit_examination->created_at)->format('d/m/Y');
                $views = ['wellmed::exports.'.request()->type.'.'.Str::kebab($morph),[
                    'workspace'         => $workspace,
                    'assessment'        => $assessment,
                    'visit_examination' => $visit_examination,
                    'visit_registration' => $visit_examination->visit_registration,
                    'patient'           => $patient
                ]];
            break;
        }
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(...$views)->setOptions([
            'enable_php'    => true,
            'enable_remote'=> true,
        ]);
        $dompdf = $pdf->getDomPDF();
        $pdf->render();
        $canvas = $dompdf->getCanvas();

        $font = $dompdf->getFontMetrics()->get_font('Helvetica', 'normal');

        $canvas->page_text(
            // 260,
            40,
            820,
            "Halaman {PAGE_NUM} dari {PAGE_COUNT} | Dicetak pada ".date('d/m/Y H:i'),
            $font,
            9,
            [0, 0, 0]
        );

        return $pdf->stream();
    }
}