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
     * Get all students.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        if (request()->has('search')) {
            $this->model = $this->model->whereHas('user', function ($query) {
                $query->where('name', 'like', '%' . request()->get('search') . '%')
                    ->orWhere('email', 'like', '%' . request()->get('search') . '%')
                    ->orWhere('phone', 'like', '%' . request()->get('search') . '%');
            });
        }
        return $this->model->all();
    }

    /**
     * Get all students with pagination
     * 
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllWithPagination($perPage = 10)
    {
        if (request()->has('search')) {
            $this->model = $this->model->whereHas('user', function ($query) {
                $query->where('name', 'like', '%' . request()->get('search') . '%')
                    ->orWhere('email', 'like', '%' . request()->get('search') . '%')
                    ->orWhere('phone', 'like', '%' . request()->get('search') . '%');
            });
        }
        return $this->model->paginate($perPage);
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

    /**
     * Get Student by Email
     * 
     * @param string $email
     * @return Student
     */
    public function getByEmail($email)
    {
        return $this->model->whereHas('user', function ($query) use ($email) {
            $query->where('email', $email);
        })->first();
    }
}
