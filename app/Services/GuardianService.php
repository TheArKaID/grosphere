<?php

namespace App\Services;

use App\Models\Guardian;
use Illuminate\Support\Facades\DB;

class GuardianService
{
    private $parents, $userService, $studentService;

    public function __construct(
        Guardian $parents,
        UserService $userService,
        StudentService $studentService
    ) {
        $this->parents = $parents;
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
     * Get Guardian By Id
     * 
     * @param int $id
     * @return Guardian
     */
    public function getById(int $id)
    {
        return $this->parents->findOrFail($id);
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
        $parent = $this->parents->create($data);

        $user->assignRole('parent');

        DB::commit();

        return $parent;
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

        $parent = $this->getById($id);

        $parent->update($data);
        $parent->user->update($data);

        DB::commit();

        return $parent;
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
     * @param Guardian $guardian
     * @param int $child_id
     * 
     * @return bool
     */
    public function addChild(Guardian $guardian, int $child_id)
    {
        return $guardian->students()->attach($child_id);
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
        $parent = $this->getById($id);

        $this->userService->changePassword($parent->user->id, $password);

        return true;
    }
}
