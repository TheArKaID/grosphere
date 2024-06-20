<?php

namespace App\Http\Requests;

use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClassGroupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:25500',
            'teacher_id' => 'required|string|exists:teachers,id',
            'students' => 'sometimes|array',
            'students.*' => [
                'sometimes', 'string', 'exists:students,id',
                Rule::exists('students', 'id')->where(function (Builder $query) {
                    return $query->leftJoinWhere('users', 'users.id', '=', 'students.id')
                        ->leftJoinWhere('users', 'users.agency_id', '=', $this->user()->agency_id);
                }),
            ],
        ];
    }
}
