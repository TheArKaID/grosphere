<?php

namespace App\Services;

use App\Contracts\StudentRepositoryContract;
use App\Contracts\UserRepositoryContract;
use App\Http\Resources\StudentCollection;
use App\Http\Resources\StudentResource;
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
	 * Get all Student
	 *
	 * @return mixed
	 */
	public function getAll()
	{
		if (request()->has('page') && request()->get('page') == 'all') {
			return new StudentCollection($this->studentRepository->getAll());
		}
		return new StudentCollection($this->studentRepository->getAllWithPagination(request('size', 10)));
	}

	/**
	 * Get Student by id
	 * 
	 * @param $id
	 * @return mixed
	 */
	public function getById($id)
	{
		return new StudentResource($this->studentRepository->getById($id));
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
	 * Update Student
	 *
	 * @param $id
	 * @param array $data
	 * @return App\Models\Student
	 */
	public function updateStudent($id, $data)
	{
		DB::beginTransaction();

		$student = new StudentResource($this->studentRepository->update($id, $data));

		$this->userRepository->update($student->user_id, $data);

		DB::commit();

		return $student;
	}

	/**
	 * Delete Student
	 *
	 * @param $id
	 * @return boolean
	 */
	public function deleteStudent($id)
	{
		DB::beginTransaction();

		$student = $this->studentRepository->getById($id);

		$this->studentRepository->delete($id);
		$this->userRepository->delete($student->user_id);

		DB::commit();

		return true;
	}
}
