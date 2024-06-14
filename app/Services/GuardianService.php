<?php

namespace App\Services;

use App\Models\Guardian;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class GuardianService
{
    private $guardians, $userService, $studentService;

    public function __construct(
        Guardian $guardians,
        UserService $userService,
        StudentService $studentService
    ) {
        $this->guardians = $guardians;
        $this->userService = $userService;
        $this->studentService = $studentService;
    }

    /**
     * Get All Guardian
     * 
     * @return Guardian
     */
    public function getAll()
    {
        if (request()->has('search')) {
            $this->guardians = $this->guardians->whereHas('user', function ($query) {
                $query->where('first_name', 'like', '%' . request()->get('search') . '%')
                    ->orWhere('last_name', 'like', '%' . request()->get('search') . '%')
					->orWhere('email', 'like', '%' . request()->get('search') . '%')
					->orWhere('username', 'like', '%' . request()->get('search') . '%')
                    ->orWhere('phone', 'like', '%' . request()->get('search') . '%');
            });
        }

        if ($studentId = request()->get('student_id', false)) {
            return $this->getByStudentId($studentId);
        }

        if (request()->has('page') && request()->get('page') == 'all') {
            return $this->guardians->get();
        }
        return $this->guardians->paginate(request('size', 10));
    }

    /**
     * Count Guardians
     * 
     * @return int
     */
    public function count()
    {
        return $this->guardians->count();
    }

    /**
     * Get Guardian By Id
     * 
     * @param int $id
     * @return Guardian
     */
    public function getById(int $id)
    {
        return $this->guardians->findOrFail($id);
    }

    /**
     * Create Guardian
     * 
     * @param array $data
     * @return Guardian
     */
    public function create(array $data)
    {
        DB::beginTransaction();

        $data['password'] = bcrypt($data['password']);

		$data['agency_id'] = auth()->user()->agency_id;

        $user = $this->userService->createUser($data);
        $data['user_id'] = $user->id;
        $guardian = $this->guardians->create($data);

        // Profile is image base64 encoded
        // Decode to image and store to s3
        $data['photo'] = base64_decode(substr($data['photo'], strpos($data['photo'], ",")+1));
        Storage::disk('s3')->put('guardians/' . $guardian->id . '.png', $data['photo']);

        $user->assignRole('guardian');

        // There's Student IDs, sync them
        $this->syncStudents($guardian, $data['student_ids']);

        DB::commit();

        return $guardian;
    }

    /**
     * Update Guardian
     * 
     * @param int $id
     * @param array $data
     * @return Guardian
     */
    public function update(int $id, array $data)
    {
        DB::beginTransaction();

        $guardian = $this->getById($id);

        $guardian->update($data);
		$this->userService->updateUser($guardian->user_id, $data);

        $this->syncStudents($guardian, $data['student_ids']);

        if ($photo = $data['photo'] ?? false) {
            $photo = base64_decode(substr($photo, strpos($photo, ",")+1));
            Storage::disk('s3')->put('guardians/' . $guardian->id . '.png', $photo);
        }

        DB::commit();

        return $guardian;
    }

    /**
     * Delete Guardian
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id)
    {
        DB::beginTransaction();

        $guardian = $this->getById($id);
        $user_id = $guardian->user_id;
        $guardian->delete();
        $this->userService->deleteUser($user_id);

        DB::commit();

        return true;
    }

    /**
     * Sync Students
     * 
     * @param Guardian $guardian
     * @param array $children
     * 
     * @return bool
     */
    public function syncStudents(Guardian $guardian, array $children)
    {
        return $guardian->students()->sync($children);
    }

    /**
     * Change Guardian Password
     * 
     * @param int $id
     * @param string $password
     * 
     * @return bool
     */
    public function changePassword(int $id, string $password)
    {
        $guardian = $this->getById($id);

        $this->userService->changePassword($guardian->user->id, $password);

        return true;
    }

    /**
     * Get Guardians by Student ID
     * 
     * @param int $student_id
     * 
     * @return Collection
     */
    public function getByStudentId(int $student_id)
    {
        return $this->guardians->whereHas('students', function ($query) use ($student_id) {
            $query->where('student_id', $student_id);
        })->get();
    }

    /**
     * Check if the guardian is the student's guardian
     * 
     * @param int $guardian_id
     * @param int $student_id
     * 
     * @return bool
     */
    public function isGuardian(int $guardian_id, int $student_id)
    {
        return $this->guardians->where('id', $guardian_id)->whereHas('students', function ($query) use ($student_id) {
            $query->where('student_id', $student_id);
        })->exists();
    }
}
