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
     * @param int $courseWorkId
     * @param int $studentId
     * 
     * @return Collection
     */
    public function getAll(int $courseWorkId, int $studentId)
    {
        $courseStudent = $this->courseStudentService->getByCourseIdAndStudentId($courseWorkId, $studentId);

        return $this->model->where('course_student_id', $courseStudent->id)->get();
    }

    /**
     * Get all formatted Ask Answers
     * 
     * @param int $courseWorkId
     * @param int $studentId
     * 
     * @return Collection
     */
    public function getAllFormatted(int $courseWorkId, int $studentId)
    {
        $allQNA = $this->getAll($courseWorkId, $studentId);

        if ($allQNA->count() == 0) {
            return false;
        }
        $course_name = $allQNA[0]->courseStudent->courseWork->class->name;
        $tutor_name = $allQNA[0]->courseStudent->courseWork->class->tutor->user->name;
        $new = $allQNA->map(function ($item) {
            return [
                'from' => $item->from == 1 ? 'Student' : 'Tutor',
                'message' => $item->message,
                'time' => Carbon::parse($item->created_at)->format('d-m-Y H:i:s'),
            ];
        });

        return [
            'course_name' => $course_name,
            'tutor_name' => $tutor_name,
            'messages' => $new
        ];
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
        $courseWorks = $this->courseWorkService->getAll($tutorId);

        $askAnswers = [];
        foreach ($courseWorks as $courseWork) {
            $new = [
                'course_work_id' => $courseWork->id,
                'course_name' => $courseWork->class->name
            ];
            foreach ($courseWork->courseStudents as $cs) {
                $new['course_student_id'] = $cs->id;
                $new['student_name'] = $cs->student->user->name;
                $new['unread'] = $cs->askAnswers()->where('updated_at', null)->where('from', AskAnswer::$FROM_STUDENT)->count();
                $askAnswers[] = $new;
            }
        }

        return $askAnswers;
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
        $courseStudent = $this->courseStudentService->getByCourseIdAndStudentId($data['course_work_id'], $data['student_id']);
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
        $data['course_student_id'] = $data['course_student_id'];
        $data['from'] = AskAnswer::$FROM_TUTOR;
        $data['updated_at'] = null;

        return $this->model->create($data);
    }
}
