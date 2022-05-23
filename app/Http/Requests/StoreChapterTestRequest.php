<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreChapterTestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->hasRole('admin|tutor');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type' => 'required|integer|in:1,2',
            'title' => 'required_if:type,1|string|max:255',
            'duration' => 'required_if:type,1|integer',
            'attempt' => 'required_if:type,1|integer|min:1',
            'file' => 'required_if:type,2|nullable|file|mimes:pdf,doc,docx,zip,rar',
            'available_at' => 'nullable|date_format:d-m-Y H:i:s',
            'available_until' => 'nullable|date_format:d-m-Y H:i:s',
        ];
    }
}
