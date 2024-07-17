<?php

namespace App\Http\Requests;

use App\Rules\OnlyUniqueEmailOrUsername;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreStudentRequest extends FormRequest
{
    /**
     * Indicates if the validator should stop on the first rule failure.
     *
     * @var bool
     */
    protected $stopOnFirstFailure = true;

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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'identifier' => ['required', 'string', 'max:255', 'min:4', new OnlyUniqueEmailOrUsername],
            'password' => ['required', 'confirmed', Password::min(8)],
            'phone' => 'nullable|string|min:8|max:50',
            'birth_date' => 'nullable|date_format:Y-m-d',
            'birth_place' => 'nullable|string|max:100',
            'gender' => 'nullable|string|between:M,F',
            'address' => 'nullable|string',
            'photo' => 'required|string',
            'id_number' => 'required|string|max:25',
        ];
    }

    public function messages()
    {
        return [
            'password.confirmed' => 'Password confirmation does not match',
            'password.min' => 'Password must be at least 8 characters',
            'password.letters' => 'Password must contain at least one letter',
            'password.numbers' => 'Password must contain at least one number',
            'password.mixed' => 'Password must contain at least one uppercase and one lowercase letter',
        ];
    }
}
