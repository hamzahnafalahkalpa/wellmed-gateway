<?php

namespace Projects\WellmedGateway\Requests\API\Setting\SatuSehat\GeneralSetting;

use Hanafalah\LaravelSupport\Requests\FormRequest;

class ShowRequest extends FormRequest
{
    protected $__entity = 'SatuSehatLog';

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
        return [];
    }
}
