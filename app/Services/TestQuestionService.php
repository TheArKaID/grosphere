<?php

namespace App\Services;

use App\Models\TestQuestion;
use Illuminate\Support\Facades\DB;

class TestQuestionService
{
    private $model, $chapterTestService;

    public function __construct(TestQuestion $model, ChapterTestService $chapterTestService)
    {
        $this->model = $model;
        $this->chapterTestService = $chapterTestService;
    }

    /**
     * Get all test questions
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        if (request()->has('search')) {
            $this->model = $this->model->with('testAnswers')->whereHas('question', function ($query) {
                $query->where('question', 'like', '%' . request()->get('search') . '%');
            });
        }
        if (request()->has('page') && request()->get('page') == 'all') {
            return $this->student->get();
        }
        return $this->model->paginate(request('size', 10));
    }

    /**
     * Add Question to Chapter Test
     * 
     * @param array $data
     * 
     * @return TestQuestion
     */
    public function addQuestion(array $data)
    {
        DB::beginTransaction();
        $chapterTest = $this->chapterTestService->getOne($data['course_chapter_id'], $data['tutor_id']);
        $data['chapter_test_id'] = $chapterTest->id;
        $data['answer_number'] = $data['type'] == TestQuestion::$MULTIPLE_CHOICE ? $data['type'] : null;
        $question = $chapterTest->testQuestions()->create($data);

        if ($data['type'] == $this->model::$MULTIPLE_CHOICE) {
            $this->addAnswers($question, $data);
        }
        DB::commit();
        return $question;
    }

    /**
     * Add answers to question
     * 
     * @param TestQuestion $question
     * @param array $data
     * 
     * @return TestQuestion
     */
    public function addAnswers($question, $data)
    {
        $question->testAnswers()->createMany([
            [
                'test_question_id' => $question->id,
                'answer' => $data['answer_1'],
                'number' => 1
            ],
            [
                'test_question_id' => $question->id,
                'answer' => $data['answer_2'],
                'number' => 2
            ],
            [
                'test_question_id' => $question->id,
                'answer' => $data['answer_3'],
                'number' => 3
            ],
            [
                'test_question_id' => $question->id,
                'answer' => $data['answer_4'],
                'number' => 4
            ]
        ]);
    }
}
