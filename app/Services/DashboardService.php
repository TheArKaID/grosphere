<?php

namespace App\Services;

use App\Models\Agenda;
use Carbon\CarbonPeriod;
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
     * Get Attendance Summary
     * 
     * @param string $filter
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function attendanceSummary($filter)
    {
        $attendanceService = app()->make(AttendanceService::class);
        $studentService = app()->make(StudentService::class);

        $totalStudent = $studentService->count();

        // Create an array of days using Carbon between weekly or monthly based on $filter
        if ($filter =='weekly') {
            $days = collect(CarbonPeriod::create(now()->startOfWeek(), now()->endOfWeek()))->map(function ($day) {
                return $day->format('Y-m-d');
            });
        
        } else {
            $days = collect(CarbonPeriod::create(now()->startOfMonth(), now()->endOfMonth()))->map(function ($day) {
                return $day->format('Y-m-d');
            });
        }

        $totalStudentIn = $attendanceService->totalIn($filter);
        /**
         * The result is array of:
         * "id": 4,
         * "student_id": 1,
         * "type": "in",
         * "created_at": "2024-05-07T09:47:22.000000Z",
         * "out": null
         *  Group it by date, then count the total student in
         */
        $attendances = $totalStudentIn->groupBy(function ($attendance) {
            return Carbon::parse($attendance->created_at)->format('Y-m-d');
        })->map(function ($attendance) {
            return $attendance->count();
        });

        $newAttendances = [];
        foreach ($days as $day) {
            if (isset($attendances[$day])) {
                $newAttendances[$day] = [$attendances[$day], $totalStudent - $attendances[$day]];
            } else {
                $newAttendances[$day] = [0, $totalStudent];
            }
        }
        return $newAttendances;
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
                'student' => $attendance->student->user->first_name . ' ' . $attendance->student->user->last_name,
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
