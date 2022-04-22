<?php

namespace App\Http\Resources;

use App\Models\TestQuestion;
use Illuminate\Http\Resources\Json\JsonResource;

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

        return $this->resource ? [
            'id' => $this->id,
            'chapter_test_id' => $this->chapter_test_id,
            'question' => $this->question,
            'type' => $this->type,
            'answer_number' => $this->answer_number,
            'answers' => $this->type == TestQuestion::$MULTIPLE_CHOICE ? $this->testAnswers->map(function ($answer) {
                return [
                    'answer' => $answer->answer,
                    'number' => $answer->number,
                ];
            }) : null,
        ] : [];
    }
}
