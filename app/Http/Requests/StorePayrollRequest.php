<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StorePayrollRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        $data = [];
        foreach ($this->input('payroll_records') as $key => $value) {
            $data[] = [
                "employee_id" => $value['employee_id'],
                "employee_name" => $value['employee_name'],
                "payment_start" => $value['payment_start'],
                "payment_period" => $value['payment_period'],
                "payment_rate" => str_replace(",", "", $value['payment_rate']),
                "reg_hours" => str_replace(",", "", $value['reg_hours']),
                "reg_hour_rate" => str_replace(",", "", $value['reg_hour_rate']),
                "ot_hours" => str_replace(",", "", $value['ot_hours']),
                "ot_hour_rate" => str_replace(",", "", $value['ot_hour_rate']),
                "philhealth" => str_replace(",", "", $value['philhealth']),
                "tin" => str_replace(",", "", $value['tin']),
                "sss" => str_replace(",", "", $value['sss']),
                "pag_ibig" => str_replace(",", "", $value['pag_ibig']),
                "quarterly" => str_replace(",", "", $value['quarterly']),
                "year_end" => str_replace(",", "", $value['year_end']),
            ];
        }

        $this->merge(['payroll_records' => $data]);
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
            'payroll_records.*.employee_id' => 'required|integer|exists:employees,id',
            'payroll_records.*.employee_name' => 'required|string|max:255',
            'payroll_records.*.payment_rate' => 'required|numeric',
            'payroll_records.*.payment_start' => 'required|date',
            'payroll_records.*.payment_period' => 'required|date',
            'payroll_records.*.reg_hours' => 'required|numeric',
            'payroll_records.*.reg_hour_rate' => 'required|numeric',
            'payroll_records.*.ot_hours' => 'required|numeric',
            'payroll_records.*.ot_hour_rate' => 'required|numeric',
            'payroll_records.*.philhealth' => 'required|numeric',
            'payroll_records.*.tin' => 'required|numeric',
            'payroll_records.*.sss' => 'required|numeric',
            'payroll_records.*.pag_ibig' => 'required|numeric',
            'payroll_records.*.quarterly' => 'required|numeric',
            'payroll_records.*.year_end' => 'required|numeric',
        ];
    }
}
