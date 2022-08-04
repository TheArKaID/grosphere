<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AgoraTutorUploadFileRequest extends FormRequest
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
            'file' => 'file|mimes:jpeg,png,jpg,gif,mp4,mp3,pdf,doc,docx,xls,xlsx,csv,ppt,pptx',
        ];
    }
}
