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
     * Sets default values for date, week, month, and year based on current date
     * when they are not provided.
     * Automatically injects tenant_id from tenancy context.
     */
    protected function prepareForValidation(): void
    {
        $data = [];

        // Inject tenant_id from tenancy context automatically
        if (function_exists('tenancy') && tenancy() && tenancy()->tenant) {
            $data['search_tenant_id'] = tenancy()->tenant->id;
        }

        $searchType = $this->input('search_type', 'daily');

        // Set defaults based on search_type
        switch ($searchType) {
            case 'daily':
                // Set default date to today if not provided
                if (!$this->has('search_date')) {
                    $data['search_date'] = now()->format('Y-m-d');
                }
                break;

            case 'weekly':
                // Set default week to current week if not provided
                if (!$this->has('search_week')) {
                    $data['search_week'] = (int) now()->format('W'); // ISO-8601 week number
                }
                // Set default year for weekly if not provided
                if (!$this->has('search_year')) {
                    $data['search_year'] = (int) now()->format('Y');
                }
                break;

            case 'monthly':
                // Set default month to current month if not provided
                if (!$this->has('search_month')) {
                    $data['search_month'] = (int) now()->format('n');
                }
                // Set default year for monthly if not provided
                if (!$this->has('search_year')) {
                    $data['search_year'] = (int) now()->format('Y');
                }
                break;

            case 'yearly':
                // Set default year to current year if not provided
                if (!$this->has('search_year')) {
                    $data['search_year'] = (int) now()->format('Y');
                }
                break;
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
                Rule::in(['daily', 'weekly', 'monthly', 'yearly'])
            ],
            'search_date' => [
                'nullable',
                'date_format:Y-m-d',
                'required_if:search_type,daily'
            ],
            'search_week' => [
                'nullable',
                'integer',
                'min:1',
                'max:53',
                'required_if:search_type,weekly'
            ],
            'search_month' => [
                'nullable',
                'integer',
                'min:1',
                'max:12',
                'required_if:search_type,monthly'
            ],
            'search_year' => [
                'nullable',
                'integer',
                'min:2020',
                'max:2100',
                'required_if:search_type,weekly,monthly,yearly'
            ],
            'search_workspace_id' => [
                'nullable',
                'integer'
            ],
            'search_tenant_id' => [
                'nullable',
                'integer'
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
            'search_week' => 'week',
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
            'search_type.required' => 'The search type is required. Please specify daily, weekly, monthly, or yearly.',
            'search_type.in' => 'The search type must be one of: daily, weekly, monthly, yearly.',
            'search_date.date_format' => 'The date must be in Y-m-d format (e.g., 2026-02-02).',
            'search_date.required_if' => 'The date is required when search type is daily.',
            'search_week.integer' => 'The week must be a number between 1 and 53.',
            'search_week.min' => 'The week must be at least 1.',
            'search_week.max' => 'The week must not be greater than 53.',
            'search_week.required_if' => 'The week is required when search type is weekly.',
            'search_month.integer' => 'The month must be a number between 1 and 12.',
            'search_month.min' => 'The month must be at least 1.',
            'search_month.max' => 'The month must not be greater than 12.',
            'search_month.required_if' => 'The month is required when search type is monthly.',
            'search_year.integer' => 'The year must be a valid number.',
            'search_year.min' => 'The year must be at least 2020.',
            'search_year.max' => 'The year must not be greater than 2100.',
            'search_year.required_if' => 'The year is required when search type is weekly, monthly, or yearly.',
        ];
    }
}
