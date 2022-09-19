<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class  UpdateAgencyRequest extends FormRequest
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
            // 'key' => ['nullable', 'max:255', Rule::unique('agencies')->ignore($this->id)],
            'key' => 'nullable|max:255',
            'phone' => 'nullable|max:50',
            // 'email' => ['nullable', 'max:255', Rule::unique('agencies')->ignore($this->id)],
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|max:255',
            'address' => 'nullable',
            'about' => 'nullable|max:255',
            'sub_title' => 'nullable|max:255',
            'color' => 'nullable|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'logo_small' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }
}
