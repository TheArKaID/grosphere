<?php

namespace App\Services;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
	private $user, $liveClassService;

	public function __construct(User $user, LiveClassService $liveClassService)
	{
		$this->user = $user;
		$this->liveClassService = $liveClassService;
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
		$user = $this->user->where('email', $data['email'])->first();

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
	
    /**
     * User Join Live Class
     * 
     * @param int $id
     * 
     * @return LiveUser
     */
    public function userJoinLiveClass($id)
    {
        if (!$this->liveClassService->isLiveClassStarted($id)) {
            return false;
        }

        $userId = auth()->user()->id;
        $data = [
            'user_id' => $userId,
            'live_class_id' => $id
        ];

        return $this->liveUserService->joinOrRejoinLiveUser($data);
    }

    /**
     * User leave Live Class
     * 
     * @param int $id
     * 
     * @return bool
     */
    public function userLeaveLiveClass($id)
    {
        $liveClass = $this->liveClassService->getLiveClassById($id);
        $userId = auth()->user()->id;
        $data = [
            'user_id' => $userId,
            'live_class_id' => $liveClass->id
        ];

        return $this->liveUserService->leaveLiveUser($data);
    }
}
