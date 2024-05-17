<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
class UpdateProjectRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        $this->merge([
            "project_cost" => str_replace(",", "", $this->input('project_cost')),
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
            "project" => 'required|string|max:255',
            "location" => 'required|string|max:255',
            "client" => 'required|string|max:255',
            "description" => 'nullable|string|max:255',
            "project_cost" => 'required|numeric',
            "start_date" => 'required|date',
            "end_date" => 'required|date',
            "status" => 'required|in:Pending,Ongoing,Done,Cancelled'
        ];
    }
}
