<?php

namespace Projects\WellmedGateway\Requests\API\Setting\Company;

use Hanafalah\LaravelSupport\Requests\FormRequest;

class StoreRequest extends FormRequest
{
    protected $__entity = 'Company';

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [];
    }
}