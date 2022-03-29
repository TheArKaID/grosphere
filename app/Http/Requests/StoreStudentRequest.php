<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreStudentRequest extends FormRequest
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
            'parent_id' => 'numeric|nullable',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()->mixedCase()],
            'phone' => 'nullable|string|min:8|max:50',
            'id_number' => 'nullable|unique:students,id_number|string|max:50',
            'birth_date' => 'required|date_format:d-m-Y',
            'birth_place' => 'required|string|max:100',
            'gender' => 'required|numeric|between:0,1',
            'address' => 'required'
        ];
    }
}
