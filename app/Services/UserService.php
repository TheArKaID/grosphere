<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserService
{
	private $user, 
	// $liveClassService,
	$liveUserService;

	public function __construct(
		User $user,
		// LiveClassService $liveClassService,
		// LiveUserService $liveUserService
	) {
		$this->user = $user;
		// $this->liveClassService = $liveClassService;
		// $this->liveUserService = $liveUserService;
	}

	/**
	 * Get all users
	 *
	 * @return User
	 */
	public function getAll()
	{
		if (request()->has('page') && request()->get('page') == 'all') {
			if (request()->has('search')) {
				$this->user = $this->user->where('name', 'like', '%' . request()->get('search') . '%')
					->orWhere('email', 'like', '%' . request()->get('search') . '%');
			}
			return $this->user->get();
		}
		return $this->user->paginate(request('size', 10));
	}

	/**
	 * Get user by id
	 *
	 * @param int $id
	 * @return User
	 */
	public function getById($id)
	{
		return $this->user->findOrFail($id);
	}

	/**
	 * Create user
	 *
	 * @param array $data
	 * @return App\Models\User
	 */
	public function createUser($data)
	{
		return $this->user->create($data);
	}

	/**
	 * Create admin user
	 *
	 * @param array $data
	 * @return App\Models\User
	 */
	public function createAdminUser($data)
	{
		return $this->user->create($data);
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
		$user = $this->user->findOrFail($id);
		$user->name = $data['name'] ?? $user->name;
		$user->email = $data['email'] ?? $user->email;
		$user->phone = $data['phone'] ?? $user->phone;
		$user->status = $data['status'] ?? $user->status;
		$user->save();

		$user->detail->update($data);

		return $user;
	}

	/**
	 * Update user for specific role
	 * 
	 * @param int $id
	 * @param array $data
	 * 
	 * @return Illuminate\Database\Eloquent\Model
	 */
	public function updateUserForRole($id, $data)
	{
		DB::beginTransaction();
		$user = $this->user->findOrFail($id);
		$user->name = $data['name'] ?? $user->name;
		$user->email = $data['email'] ?? $user->email;
		$user->phone = $data['phone'] ?? $user->phone;
		$user->status = $data['status'] ?? $user->status;

		if (isset($data['photo'])) {
			$this->uploadProfileImage($id, $data['photo']);
		}

		$user->save();
		$user->detail->update($data);

		DB::commit();

		return $user;
	}

	/**
	 * Upload profile photo
	 * 
	 * @param int $id
	 * @param mix $photo
	 * 
	 * @return Illuminate\Database\Eloquent\Model
	 */
	public function uploadProfileImage($id, $photo)
	{
		return Storage::putFileAs('public/user/' . $id . '/', $photo, 'profile.png');
	}

	/**
	 * Log user in
	 * 
	 * @param array $data
	 * @return User
	 */
	public function login($data)
	{
		$user = $this->user->where('email', $data['email']);

		$user = $user->first();

		if (!$user || !Hash::check($data['password'], $user->password)) {
			return false;
		}
		return $user;
	}

	/**
	 * Delete user
	 *
	 * @param int $id
	 * @return boolean
	 */
	public function deleteUser($id)
	{
		$user = $this->user->findOrFail($id);
		return $user->delete();
	}

	/**
	 * Change user password
	 * 
	 * @param int $id
	 * @param string $password
	 * 
	 * @return User
	 */
	public function changePassword($id, $password)
	{
		$user = $this->getById($id);
		$user->password = Hash::make($password);
		$user->save();
		return $user;
	}

	// /**
	//  * User Join Live Class
	//  * 
	//  * @param int $id
	//  * 
	//  * @return LiveUser
	//  */
	// public function userJoinLiveClass($id)
	// {
	// 	if (($status = $this->liveClassService->isLiveClassStarted($id)) !== true) {
	// 		return $status;
	// 	}

	// 	$userId = auth()->user()->id;
	// 	$data = [
	// 		'user_id' => $userId,
	// 		'live_class_id' => $id
	// 	];

	// 	return $this->liveUserService->joinOrRejoinLiveUser($data);
	// }

	// /**
	//  * User leave Live Class
	//  * 
	//  * @param int $id
	//  * 
	//  * @return bool
	//  */
	// public function userLeaveLiveClass($id)
	// {
	// 	$liveClass = $this->liveClassService->getLiveClassById($id);
	// 	$userId = auth()->user()->id;
	// 	$data = [
	// 		'user_id' => $userId,
	// 		'live_class_id' => $liveClass->id
	// 	];

	// 	return $this->liveUserService->leaveLiveUser($data);
	// }
}
