<?php

namespace App\Services;

use App\Models\Student;
use Illuminate\Support\Facades\DB;

class StudentService
{
	private $userService;
	private $student;

	public function __construct(
		Student $student,
		UserService $userService
	) {
		$this->student = $student;
		$this->userService = $userService;
	}

	/**
	 * Get all Student
	 *
	 * @return Student
	 */
	public function getAll()
	{
		if (request()->has('search')) {
			$this->student = $this->student->whereHas('user', function ($query) {
				$query->where('name', 'like', '%' . request()->get('search') . '%')
					->orWhere('email', 'like', '%' . request()->get('search') . '%')
					->orWhere('phone', 'like', '%' . request()->get('search') . '%');
			});
		}
		if (request()->has('page') && request()->get('page') == 'all') {
			return $this->student->get();
		}
		return $this->student->paginate(request('size', 10));
	}

	/**
	 * Get Student by id
	 * 
	 * @param $id
	 * @return Student
	 */
	public function getById($id)
	{
		return $this->student->findOrFail($id);
	}

	/**
	 * Create Student
	 *
	 * @param array $data
	 * @return Student
	 */
	public function createStudent($data)
	{
		DB::beginTransaction();

		$data['password'] = bcrypt($data['password']);

		$user = $this->userService->createUser($data);
		$user->assignRole('student');
		$data['user_id'] = $user->id;

		$student = $this->student->create($data);

		DB::commit();

		return $student;
	}

	/**
	 * Update Student
	 *
	 * @param $id
	 * @param array $data
	 * @return Student
	 */
	public function updateStudent($id, $data)
	{
		DB::beginTransaction();

		$student = $this->student->findOrFail($id);
		$student->user_id = $data['user_id'] ?? $student->user_id;
		$student->birth_date = $data['birth_date'] ?? $student->birth_date;
		$student->birth_place = $data['birth_place'] ?? $student->birth_place;
		$student->address = $data['address'] ?? $student->address;
		$student->gender = $data['gender'] ?? $student->gender;
		$student->save();

		$this->userService->updateUser($student->user_id, $data);

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

		$student = $this->getById($id);

		$student->delete($id);
		$this->userService->deleteUser($student->user_id);

		DB::commit();

		return true;
	}

	/**
	 * Update guardian_id
	 * 
	 * @param int $id
	 * @param int $guardian_id
	 * 
	 * @return Student
	 */
	public function updateParentId(int $id, int $guardian_id)
	{
		$student = $this->getById($id);
		$student->guardians()->sync($guardian_id);

		return $student;
	}

	/**
	 * Change Student's password
	 * 
	 * @param int $id
	 * @param string $password
	 * 
	 * @return bool
	 */
	public function changePassword(int $id, string $password)
	{
		$student = $this->getById($id);

		$this->userService->changePassword($student->user_id, $password);

		return true;
	}

	/**
	 * Change Student's password by Student
	 * 
	 * @param int $id
	 * @param array $data
	 * 
	 * @return bool
	 */
	public function changePasswordByStudent(int $id, array $data)
	{
		$student = $this->getById($id);

		$this->userService->changePassword($student->user_id, $data['new_password']);

		return true;
	}

	/**
	 * Search some users of student by email
	 * 
	 * @param string $email
	 * 
	 * @return Student
	 */
	public function searchByEmail(string $email)
	{
		return $this->student->whereHas('user', function ($query) use ($email) {
			$query->where('email', '=', $email);
		})->get();
	}
}
