<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateEmployeeRequest extends FormRequest
{

    protected function prepareForValidation()
    {
        $this->merge([
            "rate" => str_replace(",", "", $this->input('rate')),
        ]);
    }
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
            'firstname' => 'required|string|max:100',
            'middlename' => 'required|string|max:100',
            'lastname' => 'required|string|max:100',
            'suffix' => 'nullable|string|max:100',
            'gender' => 'required|in:M,F',
            'blood_type' => 'nullable|integer|exists:blood_types,id',
            'birthdate' => 'required|date',
            'employee_id' => 'required|string',
            'contact_no' => 'required|string',
            'email' => 'required|email',
            'employee_type' => 'nullable|integer|exists:employee_types,id',
            'department' => 'nullable|integer|exists:departments,id',
            'position' => 'nullable|integer|exists:designations,id',
            'date_hired' => 'required|date',
            'sss' => 'required|string',
            'tin' => 'required|string',
            'pag_ibig' => 'required|string',
            'philhealth' => 'required|string',
            'rate' => 'required|numeric',
            'familyBackgound.*.name' => 'required|string|max:255',
            'familyBackgound.*.relationship' => 'required|integer|exists:relationship_types,id',
            'familyBackgound.*.address' => 'required|string|max:255',
            'familyBackgound.*.contact_no' => 'required|string|max:20',
            'familyBackgound.*.birthdate' => 'required|date',
            'familyBackgound.*.occupation' => 'required|string|max:255',
            'workExperience.*.company_name' => 'required|string|max:255',
            'workExperience.*.position' => 'required|string|max:255',
            'workExperience.*.date_from' => 'required|date',
            'workExperience.*.date_to' => 'required|date',
            'educationalAttainment.*.school_name' => 'required|string|max:255',
            'educationalAttainment.*.address' => 'required|string|max:255',
            'educationalAttainment.*.school_level' => 'required|integer|exists:educational_levels,id',
            'educationalAttainment.*.year_from' => 'required|integer',
            'educationalAttainment.*.year_to' => 'required|integer',
        ];
    }
}
