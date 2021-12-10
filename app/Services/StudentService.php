<?php

namespace App\Services;

use App\Contracts\StudentRepositoryContract;
use App\Contracts\UserRepositoryContract;
use Illuminate\Support\Facades\DB;

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
		DB::beginTransaction();

		$data['password'] = bcrypt($data['password']);
		$user = $this->userRepository->create($data);
		$user->assignRole('student');
		$data['user_id'] = $user->id;
		$student = $this->studentRepository->create($data);

		DB::commit();

		return $student;
	}

	/**
	 * Get Student by email
	 *
	 * @param string $email
	 * @return App\Models\Student
	 */
	public function getByEmail($email)
	{
		return $this->studentRepository->getByEmail($email);
	}
}
