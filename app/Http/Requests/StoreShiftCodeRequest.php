<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
class StoreShiftCodeRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        $this->merge([
            "time_in" => date("H:i", strtotime($this->input('time_in'))),
            "break_out" => date("H:i", strtotime($this->input('break_out'))),
            "break_in" => date("H:i", strtotime($this->input('break_in'))),
            "time_out" => date("H:i", strtotime($this->input('time_out'))),
            "days" => implode("|",$this->input('days')),
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
            "shift" => 'required|integer|exists:shifts,id',
            'time_in' => 'date_format:H:i',
            'break_out' => 'date_format:H:i|after:time_in',
            'break_in' => 'date_format:H:i|after:break_out',
            'time_out' => 'date_format:H:i|after:break_in',
            'days' => 'required|string|max:255',
            'status' => 'required|in:Y,N',
        ];
    }
}
