<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
            // User
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|min:8|max:50',

            // Admin

            // Parent

            // Student
            'birth_date' => 'date_format:Y-m-d',
            'birth_place' => 'string|max:255',
            'gender' => 'numeric|between:0,1',

            // Student & Parent
            'address' => 'string',
        ];
    }
}
