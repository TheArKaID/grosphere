<?php

namespace App\Services;

use App\Models\Student;
use Illuminate\Support\Facades\DB;

class StudentService
{
	private $userService;

	public function __construct(
		UserService $userService
	) {
		$this->userService = $userService;
	}

	/**
	 * Get all Student
	 *
	 * @return Student
	 */
	public function getAll()
	{
		$student = new Student;
		if (request()->has('page') && request()->get('page') == 'all') {
			if (request()->has('search')) {
				$student = $student->whereHas('user', function ($query) {
					$query->where('name', 'like', '%' . request()->get('search') . '%')
						->orWhere('email', 'like', '%' . request()->get('search') . '%')
						->orWhere('phone', 'like', '%' . request()->get('search') . '%');
				});
			}
			return $student->get();
		}
		return $student->paginate(request('size', 10));
	}

	/**
	 * Get Student by id
	 * 
	 * @param $id
	 * @return Student
	 */
	public function getById($id)
	{
		return Student::findOrFail($id);
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

		$student = Student::create($data);

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

		$student = Student::findOrFail($id);
		$student->user_id = $data['user_id'] ?? $student->user_id;
		$student->parent_id = $data['parent_id'] ?? $student->parent_id;
		$student->id_number = $data['id_number'] ?? $student->id_number;
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
}
