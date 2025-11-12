<?php

namespace Projects\WellmedGateway\Requests\API\Setting\Room;

use Hanafalah\LaravelSupport\Requests\FormRequest;

class StoreRequest extends FormRequest
{
    protected $__entity = 'Room';

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
            // 'id'                 => ['nullable', $this->idValidation('Room')],
            // 'name'               => ['required', 'string', 'max:255'],
            // 'floor'              => ['required', 'integer'],
            // 'phone'              => ['nullable', 'string', 'max:15'],
            // 'class_room_id'      => ['nullable',  $this->idValidation('ClassRoom')],
            // 'medic_service_id'   => ['nullable', 'string', $this->idValidation('MedicService')],
            // 'service_cluster_id' => ['nullable', 'string', $this->idValidation('ServiceCluster')],
            // 'building_id'        => ['required',  $this->idValidation('Building')],
            // 'employees'          => ['nullable', 'array'],
            // 'employees.*.id'     => ['required', $this->idValidation('Employee')],
            // 'employees.*.name'   => ['nullable', 'string', 'max:255'],
        ];
    }
}
