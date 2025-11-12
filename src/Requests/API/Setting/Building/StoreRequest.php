<?php

namespace Projects\WellmedGateway\Requests\API\Setting\Building;

use Hanafalah\LaravelSupport\Requests\FormRequest;
use Hanafalah\ModuleWarehouse\Contracts\Data\BuildingData;

class StoreRequest extends FormRequest
{
    protected $__entity = 'Building';

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
        $this->requestDTO(BuildingData::class);
        return [

        ];
    }
}
