<?php

namespace Projects\WellmedGateway\Controllers\API\Unicode\Autolist;

use Hanafalah\LaravelHasProps\Models\Scopes\HasCurrentScope;
use Hanafalah\LaravelSupport\Concerns\Support\HasCache;
use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Illuminate\Http\Request;
use Projects\WellmedGateway\Controllers\API\ApiController;
use Illuminate\Support\Str;
use Hanafalah\ModuleMedicService\Enums\Label as MedicServiceLabel;

class AutolistController extends ApiController{
    use HasCache, HasRequestData;

    protected $__onlies = [
    ];

    protected $__stores = [
        'ItemStuff',
    ];

    public function index(Request $request){
        $morph = Str::studly(request()->morph);
        // if (!in_array($morph,['Patient','Disease'])){
        //     request()->merge([ 
        //         'search_name'  => request()->search_name ?? request()->search_value,
        //         'search_value' => null
        //     ]);
        // }
        switch ($morph) {
            case 'Unicode':
                $datas = $this->cacheWhen(true,[
                    'name'     => 'unicode',
                    'tags'     => ['unicode', 'unicode-index'],
                    'forever' => true
                ], function() use ($morph){
                    $unicodes = $this->callAutolist($morph,function($query){
                        $query->withoutGlobalScope('flag');
                    });
                    $grouped = [];
                    foreach ($unicodes as $unicode) {
                        $flag = $unicode['flag'];
                        $grouped[$flag] ??= [];
                        $grouped[$flag][] = $unicode;
                    }
                    return $grouped;
                });

                return (isset(request()->search_flag)) ? [request()->search_flag => $datas[request()->search_flag]] : $datas;
            break;
            case 'VisitRegistration':
                return $this->callAutolist($morph,function($query){
                    $query->when(isset(request()->is_emr) && request()->is_emr,function($query){
                        $query->with('examinationSummary');
                    });
                });
            break;
            case 'ItemStuff':
                return $this->callAutolist($morph,function($query){
                    $query->withoutGlobalScope('flag')->when(isset(request()->search_flag),function($query){
                        $query->flagIn(request()->search_flag);
                    })->where('props->general_flag','ItemStuff');
                });
            break;
            case 'MedicService':
                $schema = app(config('app.contracts.'.$morph));
                $is_for_registration = isset(request()->is_for_visit_registration) && request()->is_for_visit_registration;
                if ($is_for_registration) $schema->setIsParentOnly(false);
                return $schema->autolist(request()->type,function($query) use ($is_for_registration){
                    $query->when(isset(request()->is_for_referral) && request()->is_for_referral,function($query){
                        $query->whereIn('label',[
                            MedicServiceLabel::OUTPATIENT->value,
                            MedicServiceLabel::INPATIENT->value,
                            MedicServiceLabel::LABORATORY->value,
                            MedicServiceLabel::MCU->value,
                            MedicServiceLabel::RADIOLOGY->value,
                            MedicServiceLabel::VERLOS_KAMER->value,
                            MedicServiceLabel::EMERGENCY_UNIT->value,
                            MedicServiceLabel::TREATMENT_ROOM->value
                        ]);
                    })->when($is_for_registration,function($query){
                        $query->whereIn('label',[
                            MedicServiceLabel::INPATIENT->value,
                            MedicServiceLabel::MCU->value,
                            MedicServiceLabel::RADIOLOGY->value,
                            MedicServiceLabel::VERLOS_KAMER->value,
                            MedicServiceLabel::EMERGENCY_UNIT->value,
                            MedicServiceLabel::TREATMENT_ROOM->value,
                            'UMUM', 'ORTHOPEDI', 'SUNAT', 'KECANTIKAN', 'MATA', 'THT', 'INTERNIS', 'GIGI & MULUT', 'KIA', 'ADMIN', 'VACCINE'
                        ]);
                    })->when(isset(request()->exclude_id),function($query){
                        $ids = $this->mustArray(request()->exclude_id);
                        $query->whereNotIn('id',$ids);
                    });
                });
            break;
            case 'HeadToToe':
                $schema = app(config('app.contracts.'.$morph));
                $is_flatten = isset(request()->is_flatten) && request()->is_flatten;
                if ($is_flatten) $schema->setIsParentOnly(false);
                return $schema->autolist(request()->type);
            break;
            case 'Treatment':
                return $this->callAutolist($morph,function($query){
                    $query->when(isset(request()->search_service_reference_label),function($query){
                        $query->whereHasMorph('reference',[
                            'ClinicalPathology',
                            'AnatomicalPathology',
                            'MedicalTreatment'
                        ],function($query){
                            $query->whereHas('medicalServiceTreatment',function($query){
                                $service_reference_label = $this->mustArray(request()->search_service_reference_label);
                                $query->whereIn('props->prop_service_reference->label',$service_reference_label);
                            });
                            // $model = $query->getModel();
                            // switch($model->getMorphClass()){
                            //     case 'MasterInformedConsent':
                            //     break;
                            //     default :
                            //     break;
                            // }
                        });
                    })->when(isset(request()->search_medic_service_label),function($query){
                        $query->whereHasMorph('reference',[
                            'ClinicalPathology',
                            'AnatomicalPathology',
                            'MedicalTreatment'
                        ],function($query){
                            $query->whereHas('medicalServiceTreatment',function($query){
                                $medic_service_label = request()->search_medic_service_label;
                                if (!is_array($medic_service_label) && Str::contains($medic_service_label,',')){
                                    $medic_service_label = explode(',',$medic_service_label);
                                }
                                $medic_service_label = $this->mustArray($medic_service_label);
                                $query->whereIn('props->prop_service_reference->label',$medic_service_label);
                            });
                            // $model = $query->getModel();
                            // switch($model->getMorphClass()){
                            //     case 'MasterInformedConsent':
                            //     break;
                            //     default :
                            //     break;
                            // }
                        });
                    });
                });
            break;
            case 'Room':
                return $this->callAutolist($morph,function($query){
                    $query->when(isset(request()->search_employee_id),function($query){
                        $query->whereHas('modelHasRooms',function($query){
                            $query->withoutGlobalScopes([HasCurrentScope::class])->where('model_id',request()->search_employee_id)
                                  ->where('model_type','Employee');
                        });
                    })->when(isset(request()->search_as_pharmacy),function($query){
                        $query->where('props->as_pharmacy',request()->search_as_pharmacy);
                    });
                });
            break;
            case 'Subdistrict':
            case 'Village':
                return $this->callAutolist($morph,function($query) use ($morph){
                    $query->join('provinces','provinces.id','province_id')
                          ->join('districts',function($join){
                            $join->on('districts.id','district_id')
                                 ->on('districts.province_id','provinces.id');
                          });
                    if ($morph == 'Village'){
                        $query->select('villages.*','villages.name as name')->join('subdistricts',function($join){
                            $join->on('subdistricts.id','subdistrict_id')
                                ->on('subdistricts.district_id','districts.id');
                        });
                    }else{
                        $query->select('subdistricts.*','subdistricts.name as name');
                    }
                    if (isset(request()->search_name)) {
                        $search = strtolower(request()->search_name);

                        $query->where(function ($query) use ($morph, $search) {
                            $query->whereRaw('LOWER(provinces.name) LIKE ?', ["%{$search}%"])
                                ->orWhereRaw('LOWER(districts.name) LIKE ?', ["%{$search}%"])
                                ->orWhereRaw('LOWER(subdistricts.name) LIKE ?', ["%{$search}%"]);

                            if ($morph === 'Village') {
                                $query->orWhereRaw('LOWER(villages.name) LIKE ?', ["%{$search}%"]);
                            }
                        });
                    }
                });
            break;
            case 'Workspace':
                if (isset(request()->is_satu_sehat_integration) && isset(request()->id)){
                    request()->merge([
                        'type' => 'find'
                    ]);
                    $result = $this->callAutolist($morph);
                    $satu_sehat = &$result['integration']['satu_sehat'];

                    //DUMMY DATA
                    $satu_sehat['logs'] = [
                        [
                            'label' => 'Pasien',
                            'flag' => 'patient',
                            "from" => 0,
                            "to" => 1,
                            "progress" => 0,
                            "last_updated_at" => now()
                        ],
                        [
                            'label' => 'Kunjungan',
                            'flag' => 'encounter',
                            "from" => 1,
                            "to" => 1,
                            "progress" => 100,
                            "last_updated_at" => now()
                        ],
                        [
                            'label' => 'Kunjungan',
                            'flag' => 'encounter',
                            "from" => 0,
                            "to" => 1,
                            "progress" => 0,
                            "last_updated_at" => now()
                        ],
                        [
                            'label' => 'Observasi',
                            'flag' => 'observation',
                            "from" => 0,
                            "to" => 1,
                            "progress" => 0,
                            "last_updated_at" => now()
                        ]
                    ];
                    return $result;
                }
            break;
            case 'Patient':
                if (isset(request()->is_satu_sehat_integration) && isset(request()->id)){
                    request()->merge([
                        'type' => 'find'
                    ]);
                    $result = $this->callAutolist($morph);
                    $satu_sehat = &$result['integration']['satu_sehat'];

                    //DUMMY DATA
                    $satu_sehat['logs'] = [
                        [
                            'label' => 'Kunjungan',
                            'flag' => 'encounter',
                            "from" => 1,
                            "to" => 1,
                            "progress" => 100,
                            "last_updated_at" => now()
                        ],
                        [
                            'label' => 'Kunjungan',
                            'flag' => 'encounter',
                            "from" => 0,
                            "to" => 1,
                            "progress" => 0,
                            "last_updated_at" => now()
                        ],
                        [
                            'label' => 'Observasi',
                            'flag' => 'observation',
                            "from" => 0,
                            "to" => 1,
                            "progress" => 0,
                            "last_updated_at" => now()
                        ]
                    ];
                }
                if (isset(request()->credential)){
                    $credential = request()->credential;
                    switch ($credential) {
                        case 'nik':
                            request()->replace([
                                'search_nik' => request()->search_value
                            ]);
                        break;
                        case 'nik_ibu':
                            request()->replace([
                                'search_nik_ibu' => request()->search_value
                            ]);
                        break;
                        case 'passport':
                            request()->replace([
                                'search_passport' => request()->search_value
                            ]);
                        break;
                    }
                    $result = $this->callAutolist($morph);
                    if (isset($result['data']) && count($result['data']) > 0){
                        $result = $result['data'][0];
                        return [
                            'id'         => $result['id'],
                            'ihs_number' => $result['card_identity']['ihs_number'] ?? null
                        ];
                    }else{
                        request()->replace([
                            'params' => [
                                $credential => request()->{'search_'.$credential}
                            ]
                        ]);
                        $result = app(config('app.contracts.PatientSatuSehat'))->viewPatientSatuSehatList();
                        if (isset($result) && isset($result['entry']) && count($result['entry']) > 0){
                            $result = $result['entry'][0]['resource'];
                            return [
                                'id'         => null,
                                'ihs_number' => $result['id']
                            ];
                        }else{
                            return [
                                'id'         => null,
                                'ihs_number' => null
                            ];
                        }
                    }
                    return $result;
                }else{                    
                    return $this->callAutolist($morph,function($query){
                        if (isset(request()->find_by_nik)){
                            $query->whereHasMorph('reference',['People'],function($query){
                                $query->where('props->prop_card_identity->nik',request()->find_by_nik)->limit(1);
                            });
                        }
                    });
                }
            break;
            case 'Employee':
                $search_profession_label = request()->search_profession_label;
                if (isset($search_profession_label) && !is_array($search_profession_label) && Str::contains($search_profession_label,',')){
                    $search_profession_label = explode(',',$search_profession_label);
                    $search_profession_label = $this->mustArray($search_profession_label);
                    request()->merge([
                        'search_profession_label' => $search_profession_label
                    ]);
                }
                return $this->callAutolist($morph);
            break;
            case 'Assessment':
                $patient_id = request()->search_patient_id;
                request()->merge([
                    'search_patient_id' => null
                ]);
                return $this->callAutolist($morph,function($query) use ($patient_id){
                    $query->whereHas('patientSummary',function($query) use ($patient_id){
                        $query->where('patient_id',$patient_id);
                    });
                });
            break;
            default:
                return $this->callAutolist($morph);
            break;
        }
    }

    private function callAutolist(string $morph,?callable $callback = null){
        return app(config('app.contracts.'.$morph))->autolist(request()->type,$callback);
    }
}