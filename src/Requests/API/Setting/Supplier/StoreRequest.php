<?php

namespace Projects\WellmedGateway\Requests\API\Setting\Supplier;

use Hanafalah\LaravelSupport\Requests\FormRequest;
use Hanafalah\ModuleProcurement\Contracts\Data\SupplierData;

class StoreRequest extends FormRequest
{
    protected $__entity = 'Supplier';

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
        $this->requestDTO(SupplierData::class);
        return [

        ];
    }
}
