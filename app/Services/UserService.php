<?php

namespace App\Services;

use App\Contracts\UserRepositoryContract;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

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
		if (request()->has('page') && request()->get('page') == 'all') {
			return new UserCollection($this->userRepository->getAll());
		}
		return new UserCollection($this->userRepository->getAllWithPagination());
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
	 * Update user
	 *
	 * @param array $validatedData
	 * @param int $id
	 * @return App\Models\User
	 */
	public function updateUser($validatedData, $id)
	{
		return $this->userRepository->update($validatedData, $id);
	}

	/**
	 * Log user in
	 * 
	 * @param array $data
	 * @return User
	 */
	public function login($data)
	{
		$user = User::where('email', $data['email'])->first();

		if (!$user || !Hash::check($data['password'], $user->password)) {
			return false;
		}
		return new UserResource($user);
	}
}
