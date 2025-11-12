<?php

return [
    'examination.Anthropometry'                          => ['nullable','array'],
    'examination.Anthropometry.data.weight'              => ['nullable','numeric',$this->decimal(2),'min:0','max:300','required_with:examination.Anthropometry'],
    'examination.Anthropometry.data.height'              => ['nullable','numeric',$this->decimal(2),'min:0','max:300','required_with:examination.Anthropometry'],
    'examination.Anthropometry.data.wrist_circumference' => ['nullable','numeric',$this->decimal(2),'min:0','max:300'],
    'examination.Anthropometry.data.head_circumference'  => ['nullable','numeric',$this->decimal(2),'min:0','max:300'],
    'examination.Anthropometry.data.chest_circumference' => ['nullable','numeric',$this->decimal(2),'min:0','max:300'],
    'examination.Anthropometry.data.waist_circumference' => ['nullable','numeric',$this->decimal(2),'min:0','max:300'],
    'examination.Anthropometry.data.hip_circumference'   => ['nullable','numeric',$this->decimal(2),'min:0','max:300'],
    'examination.Anthropometry.data.arm_circumference'   => ['nullable','numeric',$this->decimal(2),'min:0','max:300'],
    'examination.Anthropometry.data.calf_circumference'  => ['nullable','numeric',$this->decimal(2),'min:0','max:300'],
    'examination.Anthropometry.data.skinfold_thickness'  => ['nullable','numeric',$this->decimal(2),'min:0','max:300']
];