<?php

namespace Projects\WellmedGateway\Requests\API\Setting\JobDesk;

use Hanafalah\LaravelSupport\Requests\FormRequest;
use Hanafalah\ModuleProfession\Enums\Profession\Flag;

class StoreRequest extends FormRequest
{
    protected $__entity = 'JobDesk';

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
        request()->merge([
            'flag' => Flag::JOB_DESK->value
        ]);

        return [
            'id'   => ['nullable',$this->idValidation($this->__entity)],
            'name' => ['required', 'string', 'max:255'],
        ];
    }
}
