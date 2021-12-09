<?php

namespace App\Services;

use App\Contracts\UserRepositoryContract;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;

class UserService
{
	private $userRepository;

	public function __construct(UserRepositoryContract $userRepository)
	{
		$this->userRepository = $userRepository;
	}

	/**
	 * Get all users
	 *
	 * @return mixed
	 */
	public function getAll()
	{
		if(request()->has('page') && request()->get('page') == 'all') {
			return new UserCollection($this->userRepository->getAll());
		}
		return new UserCollection($this->userRepository->getAllWithPagination());
	}

	/**
	 * Create user
	 *
	 * @param array $validatedData
	 * @return App\Models\User
	 */
	public function createUser($validatedData)
	{
		return $this->userRepository->create($validatedData);
	}

	/**
	 * Get user by email
	 *
	 * @param string $email
	 * @return App\Models\User
	 */
	public function getByEmail($email)
	{
		return $this->userRepository->getByEmail($email);
	}
}
