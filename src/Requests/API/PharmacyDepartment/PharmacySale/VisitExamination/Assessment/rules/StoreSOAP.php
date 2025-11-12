<?php

return [    
    'examination.SOAP'                        => ['nullable','array'],
    'examination.SOAP.data.*.subjective'      => ['required_with:examination.SOAP','string'],
    'examination.SOAP.data.*.objectives'      => ['nullable','array'],
    'examination.SOAP.data.*.objectives.*'    => ['nullable','string'],
    'examination.SOAP.data.*.assessment'      => ['nullable','string'],
    'examination.SOAP.data.*.plannings'       => ['nullable','array'],
    'examination.SOAP.data.*.plannings.*'     => ['nullable','string'],
];