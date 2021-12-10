<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class CreateStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'user_id' => 'required|exists:users,id',
            'parent_id' => 'number|nullable',
            'id_number' => 'required|number|max:50',
            'birth_date' => 'required|date:Y-m-d',
            'birth_place' => 'required|string|max:100',
            'gender' => 'required|number|between:0,1',
            'address' => 'required'
        ];
    }
    
    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        $response = new Response([
            'status' => 400,
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 400);
        throw new ValidationException($validator, $response);
    }
}
