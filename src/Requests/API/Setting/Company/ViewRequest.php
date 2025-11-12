<?php

namespace Projects\WellmedGateway\Requests\API\Setting\Company;

use Illuminate\Support\Facades\Gate;
use Hanafalah\LaravelSupport\Requests\FormRequest;

class ViewRequest extends FormRequest
{
    protected $__entity = 'Company';

    public function authorize()
    {
        return true;    
        // return Gate::allows('view', $this->route('funding'));
    }

    public function rules()
    {
        return [];
    }
}