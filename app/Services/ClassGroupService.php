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
    public function getAll()
    {
        if (request()->has('search')) {
            $search = request()->get('search');
            $this->model = $this->search($search);
        }

        if (request()->has('page') && request()->get('page') == 'all') {
            return $this->model->get();
        }

        return $this->model->paginate(request('size', 10));
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
}
