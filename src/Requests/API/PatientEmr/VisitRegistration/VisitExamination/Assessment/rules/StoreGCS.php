<?php

return [
    'examination.GCS'                => ['nullable','array'],
    'examination.GCS.data.eyes_id'   => ['required_with:examination.GCS','numeric',$this->idValidation('ItemStuff')],
    'examination.GCS.data.verbal_id' => ['required_with:examination.GCS','numeric',$this->idValidation('ItemStuff')],
    'examination.GCS.data.motor_id'  => ['required_with:examination.GCS','numeric',$this->idValidation('ItemStuff')],
];