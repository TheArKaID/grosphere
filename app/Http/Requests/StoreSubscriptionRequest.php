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
            'invoice_number' => 'required|string',
            'currency' => 'nullable|string',
            'price' => 'required|integer',
            'total_meeting' => 'required|integer',

            'active_days' => 'required|integer',
            // 'active_until' => 'required|date',
            'due_date' => 'required|date',
            'expired_date' => 'required|date',

        ];
    }
}
