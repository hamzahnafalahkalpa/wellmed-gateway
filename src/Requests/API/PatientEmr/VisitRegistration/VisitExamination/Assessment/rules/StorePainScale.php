<?php

return [
    'examination.PainScale'                   => ['nullable','array'],
    'examination.PainScale.data.rating_scale' => ['required_with:examination.PainScale','numeric']
];