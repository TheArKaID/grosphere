<?php

namespace App\Services;

use App\Models\Guardian;
use Illuminate\Support\Facades\DB;

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
                $query->where('name', 'like', '%' . request()->get('search') . '%')
                    ->orWhere('email', 'like', '%' . request()->get('search') . '%')
                    ->orWhere('phone', 'like', '%' . request()->get('search') . '%');
            });
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

        $user = $this->userService->createUser($data);
        $data['user_id'] = $user->id;
        $guardian = $this->guardians->create($data);

        $user->assignRole('guardian');

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
        $guardian->user->update($data);

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

        if ($guardian->students) {
            foreach ($guardian->students as $key => $student) {
                $student->guardian_id = null;
                $student->save();
            }
        }

        $guardian->delete();
        $this->userService->deleteUser($guardian->user_id);

        DB::commit();

        return true;
    }

    /**
     * Add Student
     * 
     * @param Guardian $guardian
     * @param array $children
     * 
     * @return bool
     */
    public function addStudent(Guardian $guardian, array $children)
    {
        foreach ($children as $key => $child) {
            $student = $this->studentService->getById($child);
            $guardian->students()->firstOrCreate([
                'student_id' => $student->id
            ]);
        }

        return true;
    }

    /**
     * Remove Student
     * 
     * @param Guardian $guardian
     * @param int $studentId
     * 
     * @return bool
     */
    public function removeStudent(Guardian $guardian, int $studentId)
    {
        $guardian->students()->where('student_id', $studentId)->delete();

        return true;
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
}
