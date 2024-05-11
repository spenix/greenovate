<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateEmpBasicSalaryRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        $this->merge([
            "basic_salary" => str_replace(",", "", $this->input('basic_salary')),
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
            'department' => 'required|integer|exists:departments,id',
            'position' => 'required|integer|exists:designations,id',
            'basic_salary' => 'required|numeric',
            'status' => 'required|in:Y,N',
        ];
    }
}
