<?php

namespace Projects\WellmedGateway\Requests\API\Setting\Organization;

use Hanafalah\LaravelSupport\Requests\FormRequest;

class StoreRequest extends FormRequest
{
    protected $__entity = 'Organization';

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [];
    }
}