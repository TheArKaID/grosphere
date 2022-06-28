<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInstituteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->hasRole('super-admin');
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
            'address' => 'nullable',
            'phone' => '|nullable|max:50',
            'email' => 'nullable|email|max:255|unique:institutes',
            'website' => 'nullable|max:255',
            'about' => 'nullable|max:255',
        ];
    }
}
