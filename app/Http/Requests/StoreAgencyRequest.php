<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAgencyRequest extends FormRequest
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
            'key' => 'required|string|max:255|unique:agencies',
            'phone' => 'nullable|max:50',
            'email' => 'nullable|email|max:255|unique:agencies',
            'address' => 'nullable',
            'website' => 'nullable|max:255',
            'about' => 'nullable|max:255',
            'sub_title' => 'nullable|max:255',
            'color' => 'nullable|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }
}
