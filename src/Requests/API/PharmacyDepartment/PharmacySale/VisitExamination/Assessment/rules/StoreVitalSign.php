<?php

return [
    'examination.VitalSign'                       => ['nullable','array'],
    'examination.VitalSign.data.temperature_type'  => ['nullable','string','in:ARMPIT,EAR,INFRARED,ORAL,RECTAL','required_with:examination.VitalSign.data.temperature'],
    'examination.VitalSign.data.temperature'       => ['nullable','numeric',$this->decimal(2),'required_with:examination.VitalSign.data.temperature_type'],
    'examination.VitalSign.data.systolic'          => ['required_with:examination.VitalSign','numeric'],
    'examination.VitalSign.data.diastolic'         => ['required_with:examination.VitalSign','numeric'],
    'examination.VitalSign.data.pulse_rate'        => ['nullable','numeric'],
    'examination.VitalSign.data.heart_rate'        => ['nullable','numeric'],
    'examination.VitalSign.data.respiration_rate'  => ['nullable','numeric'],
    'examination.VitalSign.data.oxygen_saturation' => ['nullable','numeric',$this->decimal(2)],
    'examination.VitalSign.data.sars_cov2_rt'      => ['nullable','boolean'],
    'examination.VitalSign.data.loc'               => ['nullable','numeric'],
];