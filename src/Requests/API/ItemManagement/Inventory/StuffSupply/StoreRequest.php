<?php

namespace Projects\WellmedGateway\Requests\API\ItemManagement\Inventory\StuffSupply;

use Hanafalah\LaravelSupport\Requests\FormRequest;
class StoreRequest extends FormRequest
{
    protected $__entity = 'StuffSupply';

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
        ];
    }
}
