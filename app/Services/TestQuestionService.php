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
     * @param int $courseChapterId
     * @param int $tutorId
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllQuestions($courseChapterId, $tutorId = null)
    {
        if (request()->has('search')) {
            $this->model = $this->model->with('testAnswers')->whereHas('question', function ($query) {
                $query->where('question', 'like', '%' . request()->get('search') . '%');
            });
        }
        $courseTest = $this->chapterTestService->getOne($courseChapterId, $tutorId);
        $this->model = $this->model->where('chapter_test_id', $courseTest->id);
        if (request()->has('page') && request()->get('page') == 'all') {
            return $this->student->get();
        }
        return $this->model->paginate(request('size', 10));
    }

    /**
     * Get one test question
     * 
     * @param int $courseChapterId
     * @param int $id
     * @param int $tutorId
     * 
     * @return TestQuestion
     */
    public function getOne($courseChapterId, $id, $tutorId = null)
    {
        $courseTest = $this->chapterTestService->getOne($courseChapterId, $tutorId);
        return $this->model->where('chapter_test_id', $courseTest->id)->findOrFail($id);
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
        $data['answer_number'] = $data['type'] == TestQuestion::$MULTIPLE_CHOICE ? $data['answer_number'] : null;
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
     * @return void
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

    /**
     * Update question 
     * 
     * @param int $id
     * @param array $data
     * 
     * @return TestQuestion
     */
    public function updateQuestion($id, array $data)
    {
        $question = $this->getOne($data['course_chapter_id'], $id, $data['tutor_id']);
        DB::beginTransaction();
        if ($data['type'] == $this->model::$MULTIPLE_CHOICE) {
            $this->updateAnswers($question, $data);
        } else {
            $data['answer_number'] = null;
            $question->testAnswers()->each(function ($answer) {
                $answer->delete();
            });
        }
        $question->update($data);
        DB::commit();
        return $question;
    }

    /**
     * Update answers
     * 
     * @param TestQuestion $question
     * @param array $data
     * 
     * @return void
     */
    public function updateAnswers($question, $data)
    {
        $question->testAnswers()->updateOrCreate(
            ['number' => 1],
            ['answer' => $data['answer_1']
        ]);
        $question->testAnswers()->updateOrCreate(
            ['number' => 2],
            ['answer' => $data['answer_2']
        ]);
        $question->testAnswers()->updateOrCreate(
            ['number' => 3],
            ['answer' => $data['answer_3']
        ]);
        $question->testAnswers()->updateOrCreate(
            ['number' => 4],
            ['answer' => $data['answer_4']
        ]);
    }

    /**
     * Delete Question from Chapter Test
     * 
     * @param int $courseChapterId
     * @param int $id
     * @param int $tutorId
     * 
     * @return boolean
     */
    public function deleteQuestion($courseChapterId, $id, $tutorId = null)
    {
        $question = $this->getOne($courseChapterId, $id, $tutorId);
        $question->delete();
        return true;
    }
}
