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
     * Get student by id.
     * 
     * @param int $id
     * @return \App\Models\Student
     */
    public function getById($id)
    {
        return $this->model->findOrFail($id);
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
        })->firstOrFail();
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
     * Update Student
     * 
     * @param int $id
     * @param array $data
     * @return Student
     */
    public function update($id, $data)
    {
        $student = $this->model->findOrFail($id);
        $student->user_id = $data['user_id'] ?? $student->user_id;
        $student->parent_id = $data['parent_id'] ?? $student->parent_id;
        $student->id_number = $data['id_number'] ?? $student->id_number;
        $student->birth_date = $data['birth_date'] ?? $student->birth_date;
        $student->birth_place = $data['birth_place'] ?? $student->birth_place;
        $student->address = $data['address'] ?? $student->address;
        $student->gender = $data['gender'] ?? $student->gender;
        $student->save();

        return $student;
    }

    /**
     * Delete Student
     * 
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        return $this->model->findOrFail($id)->delete();
    }
}
