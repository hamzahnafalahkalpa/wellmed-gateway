<?php

namespace Projects\WellmedGateway\Requests\API\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     *
     * Sets default values for date, month, and year based on current date
     * when they are not provided.
     */
    protected function prepareForValidation(): void
    {
        $data = [];

        // Set default date to today if search_type is daily and date not provided
        if ($this->input('search_type') === 'daily' && !$this->has('search_date')) {
            $data['search_date'] = now()->format('Y-m-d');
        }

        // Set default month to current month if search_type is monthly and month not provided
        if ($this->input('search_type') === 'monthly' && !$this->has('search_month')) {
            $data['search_month'] = (int) now()->format('n');
        }

        // Set default year to current year if not provided for monthly or yearly
        if (in_array($this->input('search_type'), ['monthly', 'yearly']) && !$this->has('search_year')) {
            $data['search_year'] = (int) now()->format('Y');
        }

        if (!empty($data)) {
            $this->merge($data);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'search_type' => [
                'required',
                'string',
                Rule::in(['daily', 'monthly', 'yearly'])
            ],
            'search_date' => [
                'nullable',
                'date_format:Y-m-d'
            ],
            'search_month' => [
                'nullable',
                'integer',
                'min:1',
                'max:12'
            ],
            'search_year' => [
                'nullable',
                'integer',
                'min:2020',
                'max:2100'
            ],
            'search_workspace_id' => [
                'nullable',
                'string'
            ],
            'search_tenant_id' => [
                'nullable',
                'string'
            ],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'search_type' => 'search type',
            'search_date' => 'date',
            'search_month' => 'month',
            'search_year' => 'year',
            'search_workspace_id' => 'workspace ID',
            'search_tenant_id' => 'tenant ID',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'search_type.required' => 'The search type is required. Please specify daily, monthly, or yearly.',
            'search_type.in' => 'The search type must be one of: daily, monthly, yearly.',
            'search_date.date_format' => 'The date must be in Y-m-d format (e.g., 2026-01-31).',
            'search_month.integer' => 'The month must be a number between 1 and 12.',
            'search_month.min' => 'The month must be at least 1.',
            'search_month.max' => 'The month must not be greater than 12.',
            'search_year.integer' => 'The year must be a valid number.',
            'search_year.min' => 'The year must be at least 2020.',
            'search_year.max' => 'The year must not be greater than 2100.',
        ];
    }
}
