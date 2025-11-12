<?php

namespace Projects\WellmedGateway\Requests\API\Setting\Organization;

use Hanafalah\LaravelSupport\Requests\FormRequest;

class DeleteRequest extends FormRequest
{
    protected $__entity = 'Organization';

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return $this->setRules([
            'id'    => ['required']
        ]);
    }
}