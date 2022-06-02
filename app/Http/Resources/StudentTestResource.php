<?php

namespace App\Http\Resources;

use App\Models\TestQuestion;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class StudentTestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        parent::wrap('student_tests');

        return $this->resource ? [
            'student_test_id' => $this->id,
            'status' => $this->getStatus(),
            'score' => $this->getScore(),
            'student_test_answers' => $this->studentTestAnswers->map(function ($answer) {
                $res = [
                    'student_answer_id' => $answer->id,
                    'question_id' => $answer->test_question_id,
                    'question_type' => $answer->testQuestion->type,
                    'question' => $answer->testQuestion->question,
                    'is_correct' => $answer->is_correct,
                    'answer' => $answer->answer,
                ];

                if ($answer->testQuestion->type == TestQuestion::$MULTIPLE_CHOICE) {
                    $res['correct_answer'] = $answer->testQuestion->answer_number;
                    $res['answer_choices'] = $answer->testQuestion->type == TestQuestion::$MULTIPLE_CHOICE ? $answer->testQuestion->testAnswers->map(function ($answer) {
                        return [
                            'answer' => $answer->answer,
                            'number' => $answer->number,
                        ];
                    }) : null;
                }

                return $res;
            }),
        ] : [];
    }
}
