<?php

namespace App\Services;

use App\Models\Tutor;
use Illuminate\Support\Facades\DB;

class TutorService
{
    private $tutor;
    private $userService;

    public function __construct(
        Tutor $tutor,
        UserService $userService
    ) {
        $this->tutor = $tutor;
        $this->userService = $userService;
    }

    /**
     * Get All Tutor
     * 
     * @return Tutor
     */
    public function getAll()
    {
        if (request()->has('search')) {
            $this->tutor = $this->tutor->whereHas('user', function ($query) {
                $query->where('name', 'like', '%' . request()->get('search') . '%')
                    ->orWhere('email', 'like', '%' . request()->get('search') . '%')
                    ->orWhere('phone', 'like', '%' . request()->get('search') . '%');
            });
        }
        if (request()->has('page') && request()->get('page') == 'all') {
            return $this->tutor->get();
        }
        return $this->tutor->paginate(request('size', 10));
    }

    /**
     * Get Parent By Id
     * 
     * @param int $id
     * @return Tutor
     */
    public function getById(int $id)
    {
        return $this->tutor->findOrFail($id);
    }

    /**
     * Create Tutor
     * 
     * @param array $data
     * @return Tutor
     */
    public function create(array $data)
    {
        DB::beginTransaction();

        $data['password'] = bcrypt($data['password']);

        $user = $this->userService->createUser($data);
        $data['user_id'] = $user->id;
        $parent = $this->tutor->create($data);

        $user->assignRole('tutor');

        DB::commit();

        return $parent;
    }

    /**
     * Update Tutor
     * 
     * @param int $id
     * @param array $data
     * @return Tutor
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
     * Delete Tutor
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
                $student->parent_id = null;
                $student->save();
            }
        }
        $user = $parent->user;
        $parent->delete();
        $user->delete();

        DB::commit();

        return true;
    }
}
