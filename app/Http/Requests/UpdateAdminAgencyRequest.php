<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAdminAgencyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|max:50',
            'email' => 'nullable|email|max:255|unique:agencies,email,' . $this->id,
            'address' => 'nullable',
            'about' => 'nullable|max:255',
            'sub_title' => 'nullable|max:255',
            'color' => 'nullable|max:255',
        ];
    }
}
