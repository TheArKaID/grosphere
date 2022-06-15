<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLiveClassRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->hasRole('admin|tutor');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'tutor_id' => [Rule::requiredIf(auth()->user()->hasRole('admin')), 'exists:tutors,id'],
            'name' => 'required|string|max:255',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'description' => 'required|string',
            'duration' => 'required|integer',
            'start_time' => 'required|date_format:d-m-Y H:i:s',
            'mic_on' => 'required|boolean',
            'cam_on' => 'required|boolean',
        ];
    }
}
