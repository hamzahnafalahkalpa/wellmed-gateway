<?php

namespace Projects\WellmedGateway\Requests\API\Setting\Profession;

use Hanafalah\LaravelSupport\Requests\FormRequest;
use Hanafalah\ModuleProfession\Enums\Profession\Flag;

class StoreRequest extends FormRequest
{
    protected $__entity = 'Profession';

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
            'id'   => ['nullable',$this->idValidation($this->__entity)],
            'name' => ['required', 'string', 'max:255'],
        ];
    }
}
