<?php

namespace App\Services;

use App\Models\AskAnswer;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class AskAnswerService
{
    private $model, $courseStudentService;

    public function __construct(
        AskAnswer $model,
        CourseStudentService $courseStudentService
    ) {
        $this->model = $model;
        $this->courseStudentService = $courseStudentService;
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
     * Get Course Category
     * 
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

        return $this->model->create($data);
    }
}
