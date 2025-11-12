<?php

namespace Projects\WellmedGateway\Requests\API\Unicode\AnggaranStuff;

use Hanafalah\LaravelSupport\Requests\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    protected $__entity = 'AnggaranStuff';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id'    => ['nullable',$this->idValidation('AnggaranStuff')]
        ];
    }
}
