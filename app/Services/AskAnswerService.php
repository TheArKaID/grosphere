<?php

namespace App\Services;

use App\Models\AskAnswer;
use Illuminate\Support\Carbon;

class AskAnswerService
{
    private $model, $courseStudentService, $courseWorkService;

    public function __construct(
        AskAnswer $model,
        CourseStudentService $courseStudentService,
        CourseWorkService $courseWorkService
    ) {
        $this->model = $model;
        $this->courseStudentService = $courseStudentService;
        $this->courseWorkService = $courseWorkService;
    }

    /**
     * Get all Ask Answers
     * 
     * @param int $courseStudentId
     * @param int $studentId
     * 
     * @return Collection
     */
    public function getAll(int $courseStudentId, int $studentId)
    {
        $courseStudent = $this->courseStudentService->getByIdAndStudentId($courseStudentId, $studentId);

        return $this->model->where('course_student_id', $courseStudent->id)->get();
    }

    /**
     * Get all formatted Ask Answers
     * 
     * @return Collection
     */
    public function getAllFormatted()
    {
        $courseStudents = $this->courseStudentService->getAll();

        $askAnswers = [];
        foreach ($courseStudents as $cs) {
            $new = [
                'tutor_name' => $cs->courseWork->class->tutor->user->name,
                'course_work_id' => $cs->courseWork->id,
                'course_name' => $cs->courseWork->class->name,
                'course_student_id' => $cs->id,
                'unread' => $cs->askAnswers()->where('updated_at', null)->where('from', AskAnswer::$FROM_TUTOR)->count(),
                'last_message' => $cs->askAnswers->count() != 0 ? Carbon::parse($cs->askAnswers->last()->created_at)->format('d-m-Y H:i:s') : null,
            ];
            $askAnswers[] = $new;
        }

        return collect($askAnswers)->sortByDesc('last_message')->values();
    }

    /**
     * Get One formatted for Student
     * 
     * @param int $courseStudentId
     * @param int $studentId
     * 
     * @return Collection
     */
    public function getOneFormattedForStudent(int $courseStudentId, int $studentId)
    {
        $courseStudent = $this->courseStudentService->getByIdAndStudentId($courseStudentId, $studentId);
        $askAnswers = $courseStudent->askAnswers->map(function ($askAnswer) {
            if ($askAnswer->updated_at == null && $askAnswer->from == AskAnswer::$FROM_TUTOR) {
                $askAnswer->updated_at = now();
                $askAnswer->save();
            }
            return [
                'from' => $askAnswer->from == 1 ? 'Student' : 'Tutor',
                'message' => $askAnswer->message,
                'time' => Carbon::parse($askAnswer->created_at)->format('d-m-Y H:i:s'),
            ];
        });

        return $askAnswers;
    }

    /**
     * Get all formatted Ask Answers for Tutor
     * 
     * @param int $tutorId
     * 
     * @return Collection
     */
    public function getAllFormattedForTutor($tutorId)
    {
        $courseWorks = $this->courseWorkService->getAllWithNewestAskAnswers($tutorId);

        $askAnswers = [];
        foreach ($courseWorks as $courseWork) {
            $new = [
                'course_work_id' => $courseWork->id,
                'course_name' => $courseWork->class->name,
                'last_message' => null
            ];

            foreach ($courseWork->courseStudents as $cs) {
                $new['course_student_id'] = $cs->id;
                $new['student_name'] = $cs->student->user->name;
                $new['unread'] = $cs->askAnswers()->where('updated_at', null)->where('from', AskAnswer::$FROM_STUDENT)->count();
                $new['last_message'] = $cs->askAnswers->count() != 0 ? Carbon::parse($cs->askAnswers->first()->created_at)->format('d-m-Y H:i:s') : null;
                $askAnswers[] = $new;
            }
        }

        return collect($askAnswers)->sortByDesc('last_message')->values();
    }

    /**
     * Get One Formatted Ask Answer for Tutor
     * 
     * @param int $courseStudentId
     * @param int $tutorId
     * 
     * @return Collection
     */
    public function getOneFormattedForTutor(int $courseStudentId, int $tutorId)
    {
        $courseStudent = $this->courseStudentService->getByIdAndTutorId($courseStudentId, $tutorId);
        $askAnswers = $courseStudent->askAnswers->map(function ($askAnswer) {
            if ($askAnswer->updated_at == null && $askAnswer->from == AskAnswer::$FROM_STUDENT) {
                $askAnswer->updated_at = now();
                $askAnswer->save();
            }
            return [
                'from' => $askAnswer->from == 1 ? 'Student' : 'Tutor',
                'message' => $askAnswer->message,
                'time' => Carbon::parse($askAnswer->created_at)->format('d-m-Y H:i:s'),
            ];
        });

        return $askAnswers;
    }

    /**
     * @param int $id
     * 
     * @return AskAnswer
     */
    public function getById($id)
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Store Ask Answer
     * 
     * @param array $data
     * 
     * @return AskAnswer
     */
    public function store(array $data)
    {
        $courseStudent = $this->courseStudentService->getByIdAndStudentId($data['course_student_id'], $data['student_id']);
        $data['course_student_id'] = $courseStudent->id;
        $data['from'] = AskAnswer::$FROM_STUDENT;
        $data['updated_at'] = null;

        return $this->model->create($data);
    }

    /**
     * Store Ask Answer for Tutor
     * 
     * @param array $data
     * 
     *  @return AskAnswer
     */
    public function storeForTutor(array $data)
    {
        $courseStudent = $this->courseStudentService->getByIdAndTutorId($data['course_student_id'], $data['tutor_id']);
        $data['course_student_id'] = $courseStudent->id;
        $data['from'] = AskAnswer::$FROM_TUTOR;
        $data['updated_at'] = null;

        return $this->model->create($data);
    }
}
