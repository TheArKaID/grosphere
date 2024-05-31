<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClassSessionRequest extends FormRequest
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
            'teacher_id' => 'required|exists:teachers,id',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'date' => 'required|date|date_format:Y-m-d',
            'time' => 'required|date_format:H:i',
            'quota' => 'required|integer',
            'type' => 'required|in:virtual,hybrid',
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg|max:2048',

            'total_session' => 'nullable|integer',
        ];
    }
}
