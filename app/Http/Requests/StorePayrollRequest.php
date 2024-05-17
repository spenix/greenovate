<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StorePayrollRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        $compensation = [];
        foreach ($this->input('compensation') as $key => $value) {
            $compensation[] = [
                "id" => $value['id'],
                "compensation" => $value['compensation'],
                "amount" => str_replace(",", "", $value['amount']),
            ];
        }

        $deductions = [];
        foreach ($this->input('deductions') as $key => $value) {
            $deductions[] = [
                "id" => $value['id'],
                "deduction" => $value['deduction'],
                "amount" => str_replace(",", "", $value['amount']),
            ];
        }
        $this->merge(['basic_salary' => str_replace(",", "", $this->input('basic_salary')), 'compensation' => $compensation, 'deductions' => $deductions]);
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
            'employee' => 'required|integer|exists:employees,id',
            'basic_salary' => 'required|numeric',
            'period_start' => 'required|date',
            'period_end' => 'required|date',
            'working_hours' => 'required|numeric',
            'working_days' => 'required|numeric',
            'ot_hours' => 'nullable|numeric',
            'ot_compensation' => 'nullable|numeric',
            'compensation.*.id' => 'required|integer|exists:compensation,id',
            'compensation.*.amount' => 'required|numeric',
            'deductions.*.id' => 'required|integer|exists:deductions,id',
            'deductions.*.amount' => 'required|numeric',
        ];
    }
}
