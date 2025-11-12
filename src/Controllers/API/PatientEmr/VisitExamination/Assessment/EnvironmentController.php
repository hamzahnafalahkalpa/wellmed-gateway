<?php

namespace Projects\WellmedGateway\Controllers\API\PatientEmr\VisitExamination\Assessment;

use Projects\WellmedGateway\Controllers\API\PatientEmr\EnvironmentController as EnvEnvironmentController;
use Illuminate\Support\Str;

class EnvironmentController extends EnvEnvironmentController
{
    protected function getAssessment(){
        $morph  = Str::studly(request()->morph);
        request()->merge([
            'morph' => $morph,
            'search_morph' => $morph,
            'search_examination_type' => request()->examination_type ?? 'VisitExamination',
            'search_examination_id' => request()->patient_summary_id ?? request()->visit_examination_id,
        ]);
        if (isset(request()->visit_examination_id)) {
            $visit_examination = $this->VisitExaminationModel()->findOrFail(request()->visit_examination_id);
            request()->merge([
                'patient_id' => $visit_examination->patient_id,
            ]);
        }

        $data = $this->__assessment_schema->showAssessment();
        if(!isset($data)) {
            $model = $this->{$morph.'Model'}();
            $data  = (\method_exists($model,'getExams')) ? $model->getExams() : null;
        }
        return $data;

        $response = [
            'visit_examination_id' => request()->visit_examination_id,
        ];
        $exam_type = request()->exam_type;
        switch ($exam_type) {
            case 'medical-support':
            case 'prescription':
            case 'examination':
                $response[$exam_type] = [
                    $morph => [
                        'data' => $data
                    ]
                ];
            break;
            case 'treatments':
                $response[$exam_type] = $data;
            break;
        }
    }

    protected function storeAssessment(){
        request()->merge([
            'morph'            => Str::studly(request()->morph),
            'examination_type' => 'VisitExamination',
            'examination_id'   => request()->visit_examination_id
        ]);
        $config = config('app.contracts.'.request()->morph);
        $schema = isset($config) ? app($config) : $this->__assessment_schema;
        return $schema->storeAssessment();
    }
}
