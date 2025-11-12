<?php

return [
    'treatments'                => ['nullable','array'],
    'treatments.*.treatment_id' => ['required',$this->idValidation('Treatment')],
    'treatments.*.treatment_at' => ['nullable','date_format:Y-m-d H:i'],
];