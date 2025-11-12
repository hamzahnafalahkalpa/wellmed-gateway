<?php

namespace Projects\WellmedGateway\Requests\API\Setting\Payer;

use Hanafalah\LaravelSupport\Requests\FormRequest;

class StoreRequest extends FormRequest
{
    protected $__entity = 'Payer';

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [];
    }
}