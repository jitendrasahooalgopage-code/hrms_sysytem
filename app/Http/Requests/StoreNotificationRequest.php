<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\UserAppNotificastion;

class StoreNotificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Set to true if authorization is handled via middleware/gates elsewhere
        return true; 
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title'               => ['required', 'string', 'max:255'],
            'body'                => ['required', 'string'],
            'type'                => ['required', 'string', Rule::in(array_keys(UserAppNotificastion::typeList()))],
            'category'            => ['required', 'string', Rule::in(array_keys(UserAppNotificastion::categoryList()))],
            'icon'                => ['nullable', 'string', 'max:50'],
            'icon_color'          => ['nullable', 'string', 'max:50'],
            'is_broadcast'        => ['required', 'boolean'],
            'target_roles'        => ['nullable', 'array'],
            'target_roles.*'      => ['string', 'exists:roles,slug'], // ensures role slug exists in DB
            'target_employee_ids' => ['nullable', 'array'],
            'target_employee_ids.*'=> ['integer', 'exists:users,id'], // ensures employee user ID exists in DB
            'action_url'          => ['nullable', 'string', 'max:2048'],
            'action_label'        => ['nullable', 'string', 'max:100'],
            'scheduled_at'        => ['nullable', 'date', 'after_or_equal:now'],
            'action'              => ['required', 'string', Rule::in(['draft', 'schedule', 'send'])],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            // Cast incoming string representation from form toggle ('0' or '1') to true boolean
            'is_broadcast' => filter_var($this->is_broadcast, FILTER_VALIDATE_BOOLEAN),
        ]);
    }
}