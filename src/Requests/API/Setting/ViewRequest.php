<?php

namespace Projects\WellmedGateway\Requests\API\Setting;

use Hanafalah\LaravelSupport\Requests\FormRequest;

class ViewRequest extends FormRequest
{
    protected $__entity = 'Permission';
    

    public function authorize()
    {
        return true;    
    }

    public function rules()
    {
        return [];
    }
}