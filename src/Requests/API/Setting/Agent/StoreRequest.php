<?php

namespace Projects\WellmedGateway\Requests\API\Setting\Agent;

use Hanafalah\LaravelSupport\Requests\FormRequest;

class StoreRequest extends FormRequest
{
    protected $__entity = 'Agent';

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [];
    }
}