<?php

namespace App\Services;

use App\Models\Agenda;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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

    /**
     * Get Data National Holiday
     * 
     * @param string $firstDate
     * 
     * @return \Illuminate\Database\Eloquent\Collection|string
     */
    public function nationalHoliday($firstDate = null)
    {
        try {
            if ($firstDate) {
                $firstDate = Carbon::parse($firstDate)->firstOfMonth()->toDateString() . 'T00:00:00Z';
                $lastDate = Carbon::parse($firstDate)->lastOfMonth()->toDateString() . 'T23:59:59Z';
            } else {
                $firstDate = Carbon::now()->firstOfMonth()->toDateString() . 'T00:00:00Z';
                $lastDate = Carbon::now()->lastOfMonth()->toDateString() . 'T23:59:59Z';
            }

            $getHoliday = json_decode(file_get_contents('https://www.googleapis.com/calendar/v3/calendars/id.indonesian%23holiday%40group.v.calendar.google.com/events?key=AIzaSyD4rlTgr10YRzDhihGmIt3pJtdZYwtuDlc&timeMin='.$firstDate.'&timeMax='.$lastDate), true);
            $data = [];
            foreach ($getHoliday['items'] as $key => $val) {
                $holiday = $val['start']['date'];

                $data[] = [
                    'date' => $holiday,
                    'day' => Carbon::parse($holiday)->format('l'),
                    'title' => $val['summary']
                ];
            }

            return $data;
        } catch (\Illuminate\Database\QueryException $e) {
            return 'Server Kalender sedang tidak dapat diakses. Silahkan coba beberapa saat lagi.';
        }
    }
}
