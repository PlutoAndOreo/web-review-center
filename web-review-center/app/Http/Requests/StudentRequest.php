<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:rc_students,email',
            'area_code'  => 'required|string|regex:/^\+[1-9]\d{1,3}$/',
            'phone'      => 'required|string|regex:/^[0-9]{10}$/',
            'address'    => 'nullable|string|max:500',
            'school_graduated' => 'nullable|string|max:255',
            'graduation_year' => 'nullable|integer|min:1950|max:' . (date('Y') + 5),
            'password'   => 'required|string|min:6|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'area_code.required' => 'Please select an area code.',
            'area_code.regex' => 'Invalid area code format.',
            'phone.required' => 'Phone number is required.',
            'phone.regex' => 'Phone number must be 7-15 digits.',
        ];
    }
}
