<?php

namespace App\Services;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
	/**
	 * Get all users
	 *
	 * @return mixed
	 */
	public function getAll()
	{
		$users = new User;
		if (request()->has('page') && request()->get('page') == 'all') {
			if (request()->has('search')) {
				$users = $users->where('name', 'like', '%' . request()->get('search') . '%')
					->orWhere('email', 'like', '%' . request()->get('search') . '%');
			}
			return UserResource::collection($users->get());
		}
		return UserResource::collection($users->paginate(request('size', 10)));
	}

	/**
	 * Create user
	 *
	 * @param array $data
	 * @return App\Models\User
	 */
	public function createUser($data)
	{
		return User::create($data);
	}

	/**
	 * Update user
	 *
	 * @param int $id
	 * @param array $data
	 * @return App\Models\User
	 */
	public function updateUser($id, $data)
	{
		$user = User::findOrFail($id);
		$user->name = $data['name'] ?? $user->name;
		$user->email = $data['email'] ?? $user->email;
		$user->phone = $data['phone'] ?? $user->phone;
		$user->status = $data['status'] ?? $user->status;
		$user->save();
		return $user;
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

	/**
	 * Delete user
	 *
	 * @param int $id
	 * @return boolean
	 */
	public function deleteUser($id)
	{
		$user = User::findOrFail($id);
		return $user->delete();
	}
}
