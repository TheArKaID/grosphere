<?php

namespace App\Http\Requests;

use App\Models\TestQuestion;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTestQuestionRequest extends FormRequest
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
            'question' => 'required|string',
            'type' => 'required|integer|in:' . TestQuestion::$MULTIPLE_CHOICE . ',' . TestQuestion::$ESSAY,
            'answer_1' => ['string', Rule::requiredIf(function () {
                return $this->type == TestQuestion::$MULTIPLE_CHOICE;
            })],
            'answer_2' => ['string', Rule::requiredIf(function () {
                return $this->type == TestQuestion::$MULTIPLE_CHOICE;
            })],
            'answer_3' => ['string', Rule::requiredIf(function () {
                return $this->type == TestQuestion::$MULTIPLE_CHOICE;
            })],
            'answer_4' => ['string', Rule::requiredIf(function () {
                return $this->type == TestQuestion::$MULTIPLE_CHOICE;
            })],
            'answer_number' => ['nullable', 'integer', 'in:1,2,3,4', Rule::requiredIf(function () {
                return $this->type == TestQuestion::$MULTIPLE_CHOICE;
            })],
        ];
    }
}
