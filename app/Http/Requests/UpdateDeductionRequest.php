<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateDeductionRequest extends FormRequest
{

    protected function prepareForValidation()
    {
        $this->merge([
            "amount" => str_replace(",", "", $this->input('amount')),
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
            'deduction' => 'required|string|max:255',
            'short_code' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'status' => 'required|in:Y,N',
        ];
    }
}
