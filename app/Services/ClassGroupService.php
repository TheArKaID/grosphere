<?php

namespace App\Services;

use App\Models\ClassGroup;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ClassGroupService
{
    private $model;

    public function __construct(ClassGroup $model)
    {
        $this->model = $model;
    }

    /**
     * Get all ClassGroups
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll(string $teacher_id = '')
    {
        if ($search = request()->get('search')) {
            $this->model = $this->search($search);
        }

        if ($teacher_id) {
            $this->model = $this->model->where('teacher_id', $teacher_id);
        }

        if (request()->has('page') && request()->get('page') == 'all') {
            return $this->model->get();
        }

        return $this->model->paginate(request('size', 10));
    }

    /**
     * Get one ClassGroup
     * 
     * @param string $classGroupId
     * @param bool $throw
     * 
     * @return ClassGroup
     */
    public function getOne(string $classGroupId, bool $throw = true)
    {
        $classGroup = $this->model->find($classGroupId);

        if (!$classGroup && $throw) {
            throw ValidationException::withMessages(['class_group' => 'Class Group not found.']);
        }

        return $classGroup;
    }

    /**
     * Search ClassGroups
     * 
     * @param string $search
     * 
     * @return ClassGroup
     */
    public function search($search)
    {
        return $this->model->where('name', 'like', '%' . $search . '%')
            ->orWhere('description', 'like', '%' . $search . '%');
    }

    /**
     * Create Class Group
     * 
     * @param array $data
     * 
     * @return ClassGroup
     */
    public function create(array $data)
    {
        DB::beginTransaction();

        $data['agency_id'] = auth()->user()->agency_id;
        $classGroup = $this->model->create($data);

        if ($students = $data['students'] ?? false){
            $this->addStudents($classGroup, $students);
        }

        DB::commit();

        return $classGroup;
    }

    /**
     * Add Students to Class Group
     * 
     * @param ClassGroup $classGroup
     * @param array $students
     * 
     * @return void
     */
    public function addStudents(ClassGroup $classGroup, array $students)
    {
        $checkStudents = Student::whereIn('id', $students)->count();
        if ($checkStudents !== count($students)) {
            throw ValidationException::withMessages(['students' => 'One or more students do not exist.']);
        }
        $classGroup->students()->attach($students);
    }

    /**
     * Update Class Group
     * 
     * @param array $data
     * @param string $classGroupId
     * 
     * @return ClassGroup
     */
    public function update(array $data, string $classGroupId)
    {
        DB::beginTransaction();

        $classGroup = $this->model->findOrFail($classGroupId);
        $classGroup->update($data);

        $classGroup->students()->sync($data['students']);
        DB::commit();

        return $classGroup;
    }

    /**
     * Delete Class Group
     * 
     * @param string $classGroupId
     * 
     * @return void
     */
    public function delete(string $classGroupId)
    {
        DB::beginTransaction();

        $classGroup = $this->model->findOrFail($classGroupId);
        $classGroup->delete();

        DB::commit();
    }

    /**
     * Check if student is in class group
     * 
     * @param string $classGroupId
     * @param string $studentId
     * 
     * @return bool
     */
    public function checkStudent(string $classGroupId, string $studentId)
    {
        $classGroup = $this->model->findOrFail($classGroupId);
        return $classGroup->students()->where('student_id', $studentId)->exists();
    }

    /**
     * Get Class Groups by Guardian id of Students
     * 
     * @param string $guardianId
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByGuardian(string $guardianId)
    {
        return $this->model->whereHas('students', function ($query) use ($guardianId) {
            $query->whereHas('guardians', function ($query) use ($guardianId) {
                $query->where('guardian_id', $guardianId);
            });
        })->get();
    }
}
