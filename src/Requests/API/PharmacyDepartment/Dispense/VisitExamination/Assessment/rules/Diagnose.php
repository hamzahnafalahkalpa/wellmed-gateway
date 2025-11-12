<?php

use Illuminate\Validation\Rule;

return [
    'examination.InitialDiagnose'                     => ['nullable','array'],
    'examination.InitialDiagnose.data.id'             => ['nullable',$this->idValidation('Assessment')],
    'examination.InitialDiagnose.data.disease_id'     => ['nullable',$this->idValidation('Disease')],
    'examination.InitialDiagnose.data.name'           => ['nullable','required_with:examination.InitialDiagnose'],
    'examination.PrimaryDiagnose'                     => ['nullable','array'],
    'examination.PrimaryDiagnose.data.id'             => ['nullable',$this->idValidation('Assessment')],
    'examination.PrimaryDiagnose.data.disease_id'     => ['nullable',$this->idValidation('Disease')],
    'examination.PrimaryDiagnose.data.name'           => ['nullable','required_with:examination.PrimaryDiagnose'],
    'examination.SecondaryDiagnose'                   => ['nullable','array'],
    'examination.SecondaryDiagnose.data.*.id'         => ['nullable',$this->idValidation('Assessment')],
    'examination.SecondaryDiagnose.data.*.disease_id' => ['nullable',$this->idValidation('Disease')],
    'examination.SecondaryDiagnose.data.*.name'       => ['nullable','required_with:examination.SecondaryDiagnose'],
    'examination.HistoryIllness'                      => ['nullable','array'],
    'examination.HistoryIllness.data.*.id'            => ['nullable',$this->idValidation('Assessment')],
    'examination.HistoryIllness.data.*.disease_id'    => ['nullable',$this->idValidation('Disease')],
    'examination.HistoryIllness.data.*.name'          => ['nullable','required_with:examination.HistoryIllness'],
];