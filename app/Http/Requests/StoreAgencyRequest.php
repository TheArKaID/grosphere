<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAgencyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:512'],
            'about' => ['nullable', 'string', 'max:512'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:agencies,email', 'max:255'],
            'website' => ['required', 'string', 'max:255', 'unique:agencies,website'],
            'logo' => ['nullable', 'string'],
            'logo_sm' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:active,inactive'],
            'active_until' => ['nullable', 'date', 'date_format:Y-m-d H:i']
        ];
    }
}
