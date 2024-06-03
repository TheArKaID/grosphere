<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClassSessionRequest extends FormRequest
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
            'course_work_id' => 'nullable|exists:course_works,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'title' => 'nullable|string',
            'description' => 'nullable|string',
            'date' => 'nullable|date|date_format:Y-m-d',
            'time' => 'nullable|date_format:H:i',
            'quota' => 'nullable|integer',
            'type' => 'nullable|in:virtual,hybrid',
            'thumbnail' => 'nullable|string',
        ];
    }
}
