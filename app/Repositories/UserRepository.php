<?php

namespace App\Repositories;

use App\Models\User;
use App\Contracts\UserRepositoryContract;

class UserRepository implements UserRepositoryContract
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * Get all users
     * 
     * @return Collection
     */
    public function getAll()
    {
        if (request()->has('search')) {
            $this->model = $this->model->where('name', 'like', '%' . request()->get('search') . '%')
                ->orWhere('email', 'like', '%' . request()->get('search') . '%');
        }
        return $this->model->get();
    }

    /**
     * Get all users with pagination
     * 
     * @return Collection
     */
    public function getAllWithPagination()
    {
        if (request()->has('search')) {
            $this->model = $this->model->where('name', 'like', '%' . request()->get('search') . '%')
                ->orWhere('email', 'like', '%' . request()->get('search') . '%');
        }
        return $this->model->paginate();
    }

    /**
     * Get User by Email
     * 
     * @param string $email
     * @return User
     */
    public function getByEmail($email)
    {
        return $this->model->where('email', $email)->firstOrFail();
    }

    /**
     * Create User
     * 
     * @param array $validatedData
     * @return User
     */
    public function create($validatedData)
    {
        return $this->model->create($validatedData);
    }

    /**
     * Update User
     * 
     * @param int $id
     * @param array $validatedData
     * @return User
     */
    public function update($id, $validatedData)
    {
        $user = $this->model->findOrFail($id);
        $user->name = $validatedData['name'] ?? $user->name;
        $user->email = $validatedData['email'] ?? $user->email;
        $user->phone = $validatedData['phone'] ?? $user->phone;
        $user->status = $validatedData['status'] ?? $user->status;
        $user->save();
        return $user;
    }

    /**
     * Delete User
     * 
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        return $this->model->findOrFail($id)->delete();
    }
}
