<?php

namespace App\Services;

use App\Models\CourseTeacher;
use App\Models\CourseWork;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CourseWorkService
{
    private $courseWork;

    public function __construct(CourseWork $courseWork)
    {
        $this->courseWork = $courseWork;
    }

    /**
     * Get all CourseWorks
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        if (request()->has('search')) {
            $search = request()->get('search');
            $this->courseWork = $this->search($search);
        }
        if (request()->has('page') && request()->get('page') == 'all') {
            return $this->courseWork->get();
        }
        return $this->courseWork->paginate(request('size', 10));
    }

    /**
     * Get One CourseWork
     * 
     * @param int $id
     * 
     * @return CourseWork
     */
    public function getOne($id)
    {
        return $this->courseWork->findOrFail($id);
    }

    /**
     * Create CourseWork
     * 
     * @param array $data
     * 
     * @return \App\Models\CourseWork
     */
    public function create($data)
    {
        DB::beginTransaction();
        $courseWork = $this->courseWork->create($data);

        if (isset($data['teacher_id'])) {
            $courseTeacher = new CourseTeacher();
            $courseTeacher->course_work_id = $courseWork->id;
            $courseTeacher->teacher_id = $data['teacher_id'];
            $courseTeacher->save();
        }

        $thumbnailName = $courseWork->id . '.' . $data['thumbnail']->extension();
        $data['thumbnail']->storeAs('course-works', $thumbnailName, 's3');
        $courseWork->thumbnail = $data['thumbnail']->storeAs('course-works', $thumbnailName, 's3');
        $courseWork->save();

        DB::commit();
        return $courseWork;
    }

    /**
     * Update CourseWork
     * 
     * @param CourseWork $courseWork
     * @param array $data
     * 
     * @return CourseWork
     */
    public function update(CourseWork $courseWork, $data)
    {
        if (isset($data['thumbnail'])) {
            Storage::delete('course_works/' . $courseWork->thumbnail);
            $thumbnailName = $courseWork->id . '.' . $data['thumbnail']->extension();
            $data['thumbnail']->storeAs('course_works', $thumbnailName);
            $data['thumbnail'] = $thumbnailName;
        }
        $courseWork->update($data);
        return $courseWork;
    }

    /**
     * Delete CourseWork
     * 
     * @param CourseWork $courseWork
     * 
     * @return \App\Models\CourseWork
     */
    public function delete(CourseWork $courseWork)
    {
        $courseWork->delete();
        return $courseWork;
    }

    /**
     * Search in CourseWork
     * 
     * @param string $search
     * @return CourseWork
     */
    public function search($search)
    {
        return $this->courseWork->where('subject', 'like', '%' . $search . '%')
        ->orWhere('grade', 'like', '%' . $search . '%')
        ->orWhere('term', 'like', '%' . $search . '%');
    }

    /**
     * Add many teachers to CourseWork
     * 
     * @param CourseWork $courseWork
     * @param array $teachers
     * 
     * @return CourseWork
     */
    public function addTeachers(CourseWork $courseWork, $teachers)
    {
        DB::beginTransaction();
        foreach ($teachers as $teacher) {
            $courseTeacher = new CourseTeacher();
            $courseTeacher->course_work_id = $courseWork->id;
            $courseTeacher->teacher_id = $teacher;
            $courseTeacher->save();
        }
        DB::commit();
        return $courseWork;
    }

    /**
     * Remove Teacher from CourseWork
     * 
     * @param CourseWork $courseWork
     * @param int $teacherId
     * 
     * @return CourseWork
     */
    public function removeTeacher(CourseWork $courseWork, $teacherId)
    {
        $courseTeacher = CourseTeacher::where('course_work_id', $courseWork->id)
        ->where('teacher_id', $teacherId)
        ->first();
        $courseTeacher->status = 0;
        return $courseWork;
    }
}
