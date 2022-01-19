<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'string|max:255',
            'birth_date' => 'date_format:Y-m-d',
            'birth_place' => 'string|max:255',
            'gender' => 'numeric|between:0,1',
            'address' => 'string',
            'phone' => 'nullable|string|min:8|max:50',
        ];
    }
}
