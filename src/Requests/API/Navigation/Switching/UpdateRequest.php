<?php

namespace Projects\WellmedGateway\Requests\API\Navigation\Switching;

use Hanafalah\LaravelSupport\Requests\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    protected $__entity = null;

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
            'type' => ['required',Rule::in([
                'role', 'room'
            ])]
        ];
    }
}
