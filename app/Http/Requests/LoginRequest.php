<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Allow all users to submit login requests
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'email'    => 'required|string|email|max:255',
            'password' => 'required|string',
            'remember' => 'nullable|boolean',
        ];
    }

    /**
     * Prepare the data for validation (optional).
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => strtolower(trim($this->input('email'))),
        ]);
    }
}
