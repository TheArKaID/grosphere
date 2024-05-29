<?php

namespace App\Services;

use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
		if (!auth()->user()->hasRole('superadmin')) {
			$this->student = $this->student->whereHas('user', function ($query) {
				$query->agency();
			});
		}
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
     * Count Students
     * 
     * @return int
     */
    public function count()
    {
        return $this->student->count();
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

		$data['agency_id'] = auth()->user()->agency_id;

		$user = $this->userService->createUser($data);
		$user->assignRole('student');
		$data['user_id'] = $user->id;

		$student = $this->student->create($data);

        // Profile is image base64 encoded
        // Decode to image and store to s3
        $data['photo'] = base64_decode(substr($data['photo'], strpos($data['photo'], ",")+1));
        Storage::disk('s3')->put('students/' . $student->id . '.png', $data['photo']);

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
		$student->id_number = $data['id_number'] ?? $student->id_number;
		$student->save();

        if ($photo = $data['photo'] ?? false) {
            $photo = base64_decode(substr($photo, strpos($photo, ",")+1));
            Storage::disk('s3')->put('students/' . $student->id . '.png', $photo);
        }
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
	 * @param array $guardian_ids
	 * 
	 * @return Student
	 */
	public function syncGuardians(int $id, array $guardian_ids)
	{
		$student = $this->getById($id);
		$student->guardians()->sync($guardian_ids);

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

	/**
	 * Get All Student by Guardian
	 * 
	 * @param int $guardian_id
	 * 
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function getByGuardian(int $guardian_id)
	{
		return $this->student->with('user')->whereHas('guardians', function ($query) use ($guardian_id) {
			$query->where('guardian_id', $guardian_id);
		})->get();
	}
}
