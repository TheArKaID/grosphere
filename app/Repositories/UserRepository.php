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
     * Create User
     * 
     * @param array $validatedData
     * @return User
     */
    public function create($validatedData)
    {
        return $this->model->create($validatedData);
    }
}