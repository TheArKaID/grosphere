<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubscriptionRequest extends FormRequest
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
            'student_id' => 'required|exists:students,id',
            'course_work_id' => 'required|exists:course_works,id',
            'invoice_number' => 'required|unique:invoices,invoice_number',
            'currency' => 'nullable|string',
            'price' => 'required|integer',
            'total_meeting' => 'required|integer',

            'active_days' => 'required|integer',
            // 'active_until' => 'required|date',
            'due_date' => 'required|date',
            'expired_date' => 'required|date',

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
            'student_id.exists' => 'Student ID does not exist',
            'course_work_id.required' => 'Course Work ID is required',
            'course_work_id.exists' => 'Course Work ID does not exist',
            'invoice_number.required' => 'Invoice Number is required',
            'invoice_number.unique' => 'Invoice Number already exists',
            'currency.string' => 'Currency must be a string',
            'price.required' => 'Price is required',
            'price.integer' => 'Price must be an integer',
            'total_meeting.required' => 'Total Meeting is required',
            'total_meeting.integer' => 'Total Meeting must be an integer',
            'active_days.required' => 'Active Days is required',
            'active_days.integer' => 'Active Days must be an integer',
            // 'active_until.required' => 'Active Until is required',
            // 'active_until.date' => 'Active Until must be a date',
            'due_date.required' => 'Due Date is required',
            'due_date.date' => 'Due Date must be a date',
            'expired_date.required' => 'Expired Date is required',
            'expired_date.date' => 'Expired Date must be a date',
        ];
    }
}
