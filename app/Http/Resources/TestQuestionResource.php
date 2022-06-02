<?php

namespace App\Http\Resources;

use App\Models\TestQuestion;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class TestQuestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        parent::wrap('questions');

        $res = $this->resource ? [
            'id' => $this->id,
            'chapter_test_id' => $this->chapter_test_id,
            'type' => $this->type,
            'question' => $this->question,
            'answers' => $this->type == TestQuestion::$MULTIPLE_CHOICE ? $this->testAnswers->map(function ($answer) {
                return [
                    'answer' => $answer->answer,
                    'number' => $answer->number,
                ];
            }) : null,
        ] : [];

        if (Auth::user()->hasRole('tutor')) {
            $res['answer_number'] = $this->answer_number;
        }

        return $res;
    }
}
