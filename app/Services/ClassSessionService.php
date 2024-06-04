<?php

namespace App\Services;

use App\Exceptions\NotEnrolledException;
use App\Models\ClassSession;
use App\Models\StudentClass;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ClassSessionService
{
    private $classSession;

    public function __construct(ClassSession $classSession)
    {
        $this->classSession = $classSession;
    }

    /**
     * Get all ClassSessions
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        $this->classSession = $this->classSession->with(['teacher', 'courseWork']);
        if (auth()->user()->hasRole('teacher')) {
            $this->classSession = $this->classSession->where('teacher_id', auth()->user()->detail->id);
        }
        if ($search = request()->get('search', false)) {
            $this->classSession = $this->search($search);
        }
        if ($date_month = request()->get('date_month', false)) {
            $this->classSession = $this->filterByMonth($date_month);
        }
        if (request()->get('active_only', false)) {
            $this->classSession = $this->classSession->whereDate('date', '>=', date('Y-m-d'));
        }
        if (request()->has('page') && request()->get('page') == 'all') {
            return $this->classSession->get();
        }
        return $this->classSession->paginate(request('size', 10));
    }

    /**
     * Get One ClassSession
     * 
     * @param int $id
     * 
     * @return ClassSession
     */
    public function getOne($id)
    {
        $this->classSession = $this->classSession->with(['teacher', 'classMaterials', 'courseWork', 'studentClasses.courseStudent.student']);
        if (auth()->user()->hasRole('teacher')) {
            $this->classSession = $this->classSession->where('teacher_id', auth()->user()->detail->id);
        }
        return $this->classSession->findOrFail($id);
    }

    /**
     * Get Class Session detail for enrolled student
     * 
     * @param int $id
     * 
     * @return ClassSession
     */
    public function getOneForStudent($id)
    {
        if (!$this->isEnrolled($id, auth()->user()->detail->id)) {
            throw new NotEnrolledException('You are not enrolled in this class session');
        }

        $this->classSession = $this->classSession->with(['teacher', 'classMaterials', 'courseWork', 'students', 'studentClasses.courseStudent.student']);
        return $this->classSession->findOrFail($id);
    }

    /**
     * Check if student already enrolled
     * 
     * @param int $id
     * @param int $studentId
     * 
     * @return bool
     */
    public function isEnrolled($id, $studentId)
    {
        return StudentClass::where('class_session_id', $id)->where(function (Builder $query) use ($studentId) {
            $query->where('course_student_id', $studentId)->orWhere('student_id', $studentId);
        })->exists();
    }

    /**
     * Create ClassSession
     * 
     * @param array $data
     * 
     * @return \App\Models\ClassSession||\App\Models\ClassSession[]
     */
    public function create($data)
    {
        if ($totalSession = $data['total_session'] ?? false) {
            return $this->createMultiple($totalSession, $data);
        } else {
            return $this->createOne($data);
        }
    }

    /**
     * Create One Class Session
     * 
     * @param array $data
     * 
     * @return \App\Models\ClassSession
     */
    public function createOne($data)
    {
        $thumbnail = $data['thumbnail'] ?? null;
        unset($data['thumbnail']);
        $class = $this->classSession->create($data);
        $class->thumbnail = 'class-sessions/' . $class->id . '.png';

        $thumbnail = base64_decode(substr($thumbnail, strpos($thumbnail, ",")+1));
        Storage::disk('s3')->put($class->thumbnail, $thumbnail);

        $class->save();
        
        return $class;
    }

    /**
     * Create Multiple Class Session
     * 
     * @param int $totalSession
     * @param array $data
     * 
     * @return \App\Models\ClassSession[]
     */
    public function createMultiple($totalSession, $data)
    {
        $classSessions = [];
        DB::beginTransaction();
        for ($i = 0; $i < $totalSession; $i++) {
            $thumbnail = $data['thumbnail'] ?? null;
            unset($data['thumbnail']);
            $class = $this->classSession->create($data);
            $class->thumbnail = 'class-sessions/' . $class->id . '.png';
        
            $thumbnail = base64_decode(substr($thumbnail, strpos($thumbnail, ",")+1));
            Storage::disk('s3')->put($class->thumbnail, $thumbnail);

            $class->save();
            $classSessions[] = $class;
        }
        DB::commit();
        return $classSessions;
    }

    /**
     * Update ClassSession
     * 
     * @param ClassSession $classSession
     * @param array $data
     * 
     * @return ClassSession
     */
    public function update(ClassSession $classSession, $data)
    {
        // Remove null or empty data
        $data = array_filter($data, function ($value) {
            return !is_null($value) && $value !== '';
        });

        if (isset($data['thumbnail'])) {
            Storage::disk('s3')->delete($classSession->thumbnail);

            $thumbnail = $data['thumbnail'] ?? null;
            unset($data['thumbnail']);

            $thumbnail = base64_decode(substr($thumbnail, strpos($thumbnail, ",")+1));
            Storage::disk('s3')->put($classSession->thumbnail, $thumbnail);
        }
        $classSession->update($data);
        return $classSession;
    }

    /**
     * Delete ClassSession
     * 
     * @param ClassSession $classSession
     * 
     * @return \App\Models\ClassSession
     */
    public function delete(ClassSession $classSession)
    {
        $classSession->delete();
        return $classSession;
    }

    /**
     * Search in ClassSession
     * 
     * @param string $search
     * @return ClassSession
     */
    public function search($search)
    {
        return $this->classSession->where('title', 'like', '%' . $search . '%')
        ->orWhere('description', 'like', '%' . $search . '%')
        ->orWhere('date', 'like', '%' . $search . '%')
        ->orWhere('time', 'like', '%' . $search . '%');
    }

    /**
     * Get all Class Session in a month 
     * 
     * @param string $date
     * 
     * @return mixed
     */
    public function filterByMonth($date)
    {
        return $this->classSession->whereMonth('date', date('m', strtotime($date)));
    }

    /**
     * End the class session
     * 
     * @param int $id
     * @param array $data
     * 
     * @return void
     */
    public function end($id, array $data)
    {
        if (auth()->user()->hasRole('teacher')) {
            $this->classSession->where('teacher_id', auth()->id());
        }
        $classSession = $this->getOne($id);
        $classSession->summary = $data['summary'];
        
        foreach ($data['students'] as $student) {
            $classSession->studentClasses()->updateOrCreate([
                'course_student_id' => $student['id']
            ], [
                'rating' => $student['rating'],
                'remark' => $student['remark']
            ]);
        }
    }
}
