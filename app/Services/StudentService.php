<?php

namespace App\Services;

use App\Contracts\StudentRepositoryContract;
use App\Contracts\UserRepositoryContract;

class StudentService
{
	private $studentRepository;
	private $userRepository;

	public function __construct(
		StudentRepositoryContract $studentRepository,
		UserRepositoryContract $userRepository
	) {
		$this->studentRepository = $studentRepository;
		$this->userRepository = $userRepository;
	}

	/**
	 * Create Student
	 *
	 * @param array $data
	 * @return App\Models\Student
	 */
	public function createStudent($data)
	{
		$user = $this->userRepository->create($data);
		$data['user_id'] = $user->id;
		return $this->studentRepository->create($data);
	}
}
