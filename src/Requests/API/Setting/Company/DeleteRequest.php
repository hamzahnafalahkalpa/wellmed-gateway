<?php

namespace Projects\WellmedGateway\Requests\API\Setting\Company;

use Hanafalah\LaravelSupport\Requests\FormRequest;

class DeleteRequest extends FormRequest
{
    protected $__entity = 'Company';

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