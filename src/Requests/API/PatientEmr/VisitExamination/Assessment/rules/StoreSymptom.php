<?php

return [
    'examination.Symptom'             => ['nullable','array'],
    'examination.Symptom.data.*.name' => ['nullable','string','required_with:examination.Symptom']
];