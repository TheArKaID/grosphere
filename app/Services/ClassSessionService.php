<?php

namespace App\Services;

use App\Models\ClassSession;
use Illuminate\Support\Facades\DB;

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
        if (request()->has('search')) {
            $search = request()->get('search');
            $this->classSession = $this->search($search);
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
     * Create ClassSession
     * 
     * @param array $data
     * 
     * @return \App\Models\ClassSession||\App\Models\ClassSession[]
     */
    public function create($data)
    {
        if (isset($data['total_session'])) {
            return $this->createMultiple($data);
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
        $class = $this->classSession->create($data);
        $thumbnailName = $class->id . '.' . $data['thumbnail']->extension();
        $data['thumbnail']->storeAs('class-sessions', $thumbnailName, 's3');
        $class->thumbnail = $data['thumbnail']->storeAs('class-sessions', $thumbnailName, 's3');
        
        return $class;
    }

    /**
     * Create Multiple Class Session
     * 
     * @param array $data
     * 
     * @return \App\Models\ClassSession[]
     */
    public function createMultiple($data)
    {
        $classSessions = [];
        DB::beginTransaction();
        foreach ($data as $classData) {
            $class = $this->classSession->create($classData);
            $thumbnailName = $class->id . '.' . $classData['thumbnail']->extension();
            $class->thumbnail = $classData['thumbnail']->storeAs('class-sessions', $thumbnailName, 's3');
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
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function filterByDate($date)
    {
        return $this->classSession->whereMonth('date', date('m', strtotime($date)))->get();
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
