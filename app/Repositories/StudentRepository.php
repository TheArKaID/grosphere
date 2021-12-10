<?php

namespace App\Repositories;

use App\Contracts\StudentRepositoryContract;
use App\Models\Student;

class StudentRepository implements StudentRepositoryContract
{
    protected $model;

    public function __construct(Student $model)
    {
        $this->model = $model;
    }

    /**
     * Create Student
     * 
     * @param array $data
     * @return Student
     */
    public function create($data)
    {
        return $this->model->create($data);
    }
}
