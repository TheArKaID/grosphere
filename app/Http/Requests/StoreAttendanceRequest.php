<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'student_id' => 'required|integer|exists:students,id',
            'guardian_id' => 'required|integer|exists:guardians,id',
            'temperature' => 'required|string|max:255',
            'remark' => 'nullable|string|max:25500',
            'type' => 'required|string|in:in,out',
            'proof' => 'required|string',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     * 
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'student_id.required' => 'Student ID is required',
            'student_id.integer' => 'Student ID must be an integer',
            'student_id.exists' => 'Student ID does not exist',
            'guardian_id.required' => 'Guardian ID is required',
            'guardian_id.integer' => 'Guardian ID must be an integer',
            'guardian_id.exists' => 'Guardian ID does not exist',
            'temperature.required' => 'Temperature is required',
            'temperature.string' => 'Temperature must be a string',
            'temperature.max' => 'Temperature must not exceed 255 characters',
            'remark.string' => 'Remark must be a string',
            'remark.max' => 'Remark must not exceed 25500 characters',
            'type.required' => 'Type is required',
            'type.string' => 'Type must be a string',
            'type.in' => 'Type must be either in or out',
            'proof.required' => 'Picture field is required',
            'proof.string' => 'Picture must be a string',
        ];
    }
}
