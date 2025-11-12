<?php

return [
    'examination.Allergy'                          => ['nullable','array'],
    'examination.Allergy.data.*.id'                => ['nullable',$this->idValidation('Assessment')],
    'examination.Allergy.data.*.allergy_type_id'   => ['nullable','required_with:examination.Allergy.data',$this->idValidation('ExaminationStuff')],
    'examination.Allergy.data.*.name'              => ['nullable','string','required_with:examination.Allergy'],
    'examination.Allergy.data.*.allergy_scale'     => ['nullable','numeric','required_with:examination.Allergy'],
    'examination.Allergy.data.*.allergen'          => ['nullable','string'],
];