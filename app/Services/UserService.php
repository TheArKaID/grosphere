<?php 

namespace App\Services;

use App\Contracts\UserRepositoryContract;

class UserService
{	
	private $userRepository;

	public function __construct(UserRepositoryContract $userRepository)
	{
		$this->userRepository = $userRepository;
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
}