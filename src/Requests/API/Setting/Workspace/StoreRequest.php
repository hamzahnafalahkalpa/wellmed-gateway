<?php

namespace Projects\WellmedGateway\Requests\API\Setting\Workspace;

use Hanafalah\LaravelSupport\Requests\FormRequest;

class StoreRequest extends FormRequest
{
    protected $__entity = 'Workspace';

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
            'uuid'                   => ['required', 'string'],
            'name'                   => ['required', 'string'],
            'address'                => ['nullable', 'array'],
            'address.name'           => ['required_with:address','string','max:500'],
            'address.province_id'    => ['nullable',$this->idValidation('Province')],
            'address.district_id'    => ['nullable',$this->idValidation('District')],
            'address.subdistrict_id' => ['nullable',$this->idValidation('Subdistrict')],
            'address.village_id'     => ['nullable',$this->idValidation('Village')],
        ];
    }
}
