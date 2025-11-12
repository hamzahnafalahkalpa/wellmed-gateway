<?php

namespace Projects\WellmedGateway\Requests\API\Setting\PatientType;

use Hanafalah\LaravelSupport\Requests\FormRequest;

class DeleteRequest extends FormRequest
{
    protected $__entity = 'PatientType';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $patient_type = $this->usingEntity()->findOrFail($this->id);
        return $patient_type->is_delete_able ?? true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id'   => ['required',$this->idValidation($this->__entity)],
        ];
    }
}
