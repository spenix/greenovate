<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreEmployeeLeaveRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "employee_name" => 'required|integer|exists:employees,id',
            "leave_type" => 'required|integer|exists:leave_types,id',
            "leave_entitlement_id" => 'required|integer|exists:leave_entitlements,id',
            "leave_entitlement" => 'required|string',
            "date_start" => 'required|date',
            "date_end" => 'required|date',
            "leave_days" => 'required|integer',
        ];
    }
}
