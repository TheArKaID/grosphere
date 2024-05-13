<?php

namespace App\Services;

use App\Models\Agenda;
use Illuminate\Support\Carbon;

class DashboardService
{
    private $agenda;

    public function __construct(Agenda $agenda)
    {
        $this->agenda = $agenda;
    }

    /**
     * Get Users Data
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function users()
    {
        $teacherService = app()->make(TeacherService::class);
        $studentService = app()->make(StudentService::class);
        $guardianService = app()->make(GuardianService::class);

        return [
            'teachers' => $teacherService->count(),
            'students' => $studentService->count(),
            'guardians' => $guardianService->count()
        ];
    }

    /**
     * Get Calendars Data
     * 
     * @param string $date
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function calendars($date = null)
    {
        $classService = app()->make(ClassSessionService::class);

        // Only return class session's title and date time from this array of class sessions
        $calendars = $classService->filterByDate($date)->map(function ($class) {
            return [
                'title' => $class->title,
                'start' => $class->date . ' ' . $class->time
            ];
        });

        return $calendars;
    }

    /**
     * Payment Overdue Data
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function paymentOverdues()
    {
        return [];
    }

    /**
     * Attendances Data
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function attendances()
    {
        $attendanceService = app()->make(AttendanceService::class);

        return $attendanceService->pair()->map(function ($attendance) {
            return [
                'id' => $attendance->id,
                'student_id' => $attendance->student_id,
                'student' => $attendance->student->user->name,
                'guardian' => $attendance->guardian,
                'in' => Carbon::make($attendance->created_at)->format('Y-m-d H:i:s'),
                'out' => $attendance->out
            ];
        });
    }
}
