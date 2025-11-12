<?php

namespace Projects\WellmedGateway\Requests\API\Setting\Payer;

use Illuminate\Support\Facades\Gate;
use Hanafalah\LaravelSupport\Requests\FormRequest;

class ViewRequest extends FormRequest
{
    protected $__entity = 'Payer';

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