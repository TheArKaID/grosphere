<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeaveRequestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('guardian');
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
            'tag_id' => 'required|exists:leave_request_tags,id',
            'from_date' => 'required|date|date_format:Y-m-d|after_or_equal:today',
            'to_date' => 'nullable|date|date_format:Y-m-d|after_or_equal:from_date',
            'reason' => 'required|string',
            'photo' => 'nullable|string',
        ];
    }
}
