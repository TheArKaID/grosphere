<?php

namespace App\Services;

use App\Models\Parents;
use Illuminate\Support\Facades\DB;

class ParentService
{
    private $parents, $userService, $studentService;

    public function __construct(
        Parents $parents,
        UserService $userService,
        StudentService $studentService
    ) {
        $this->parents = $parents;
        $this->userService = $userService;
        $this->studentService = $studentService;
    }

    /**
     * Get All Parents
     * 
     * @return Parents
     */
    public function getAll()
    {
        if (request()->has('search')) {
            $this->parents = $this->parents->whereHas('user', function ($query) {
                $query->where('name', 'like', '%' . request()->get('search') . '%')
                    ->orWhere('email', 'like', '%' . request()->get('search') . '%')
                    ->orWhere('phone', 'like', '%' . request()->get('search') . '%');
            });
        }
        if (request()->has('page') && request()->get('page') == 'all') {
            return $this->parents->get();
        }
        return $this->parents->paginate(request('size', 10));
    }

    /**
     * Get Parent By Id
     * 
     * @param int $id
     * @return Parents
     */
    public function getById(int $id)
    {
        return $this->parents->findOrFail($id);
    }

    /**
     * Create Parents
     * 
     * @param array $data
     * @return Parents
     */
    public function create(array $data)
    {
        DB::beginTransaction();

        $data['password'] = bcrypt($data['password']);

        $user = $this->userService->createUser($data);
        $data['user_id'] = $user->id;
        $parent = $this->parents->create($data);

        $user->assignRole('parent');

        DB::commit();

        return $parent;
    }

    /**
     * Update Parents
     * 
     * @param int $id
     * @param array $data
     * @return Parents
     */
    public function update(int $id, array $data)
    {
        DB::beginTransaction();

        $parent = $this->getById($id);

        $parent->update($data);
        $parent->user->update($data);

        DB::commit();

        return $parent;
    }

    /**
     * Delete Parents
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id)
    {
        DB::beginTransaction();

        $parent = $this->getById($id);

        if ($parent->students) {
            foreach ($parent->students as $key => $student) {
                $student->guardian_id = null;
                $student->save();
            }
        }

        $parent->delete();
        $this->userService->deleteUser($parent->user_id);

        DB::commit();

        return true;
    }

    /**
     * Add Child
     * 
     * @param int $id
     * @param int $child_id
     * 
     * @return bool
     */
    public function addChild(int $id, int $child_id)
    {
        $student = $this->studentService->getById($child_id);
        $parent = $this->getById($id);

        $this->studentService->updateParentId($student->id, $parent->id);

        return true;
    }

    /**
     * Change Parent Password
     * 
     * @param int $id
     * @param string $password
     * 
     * @return bool
     */
    public function changePassword(int $id, string $password)
    {
        $parent = $this->getById($id);

        $this->userService->changePassword($parent->user->id, $password);

        return true;
    }
}
