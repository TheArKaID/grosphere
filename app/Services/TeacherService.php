<?php

namespace App\Services;

use App\Models\Teacher;
use Illuminate\Support\Facades\DB;

class TeacherService
{
    private $teacher, $userService
    // , $liveClassService
    ;

    public function __construct(
        Teacher $teacher,
        UserService $userService,
        // LiveClassService $liveClassService
    ) {
        $this->teacher = $teacher;
        $this->userService = $userService;
        // $this->liveClassService = $liveClassService;
    }

    /**
     * Get All Teacher
     * 
     * @return Teacher
     */
    public function getAll()
    {
        if (request()->has('search')) {
            $this->teacher = $this->teacher->whereHas('user', function ($query) {
                $query->where('name', 'like', '%' . request()->get('search') . '%')
                    ->orWhere('email', 'like', '%' . request()->get('search') . '%')
                    ->orWhere('phone', 'like', '%' . request()->get('search') . '%');
            });
        }
        if (request()->has('page') && request()->get('page') == 'all') {
            return $this->teacher->get();
        }
        return $this->teacher->paginate(request('size', 10));
    }

    /**
     * Get Parent By Id
     * 
     * @param int $id
     * @return Teacher
     */
    public function getById(int $id)
    {
        return $this->teacher->findOrFail($id);
    }

    /**
     * Create Teacher
     * 
     * @param array $data
     * @return Teacher
     */
    public function create(array $data)
    {
        DB::beginTransaction();

        $data['password'] = bcrypt($data['password']);

        $user = $this->userService->createUser($data);
        $data['user_id'] = $user->id;
        $teacher = $this->teacher->create($data);

        $user->assignRole('teacher');

        DB::commit();

        return $teacher;
    }

    /**
     * Update Teacher
     * 
     * @param int $id
     * @param array $data
     * @return Teacher
     */
    public function update(int $id, array $data)
    {
        DB::beginTransaction();

        $teacher = $this->getById($id);

        $teacher->update($data);
        $teacher->user->update($data);

        DB::commit();

        return $teacher;
    }

    /**
     * Delete Teacher
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id)
    {
        DB::beginTransaction();

        $teacher = $this->getById($id);

        $teacher->delete();
        $this->userService->deleteUser($teacher->user_id);

        DB::commit();

        return true;
    }

    /**
     * Change Teacher Password
     * 
     * @param int $id
     * @param string $password
     * 
     * @return bool
     */
    public function changePassword(int $id, string $password)
    {
        $teacher = $this->getById($id);

        $this->userService->changePassword($teacher->user_id, $password);

        return true;
    }

    /**
     * Change Teacher's password By Teacher
     * 
     * @param int $id
     * @param array $data
     * 
     * @return bool
     */
    public function changePasswordByTeacher(int $id, array $data)
    {
        $teacher = $this->getById($id);

        $this->userService->changePassword($teacher->user_id, $data['new_password']);

        return true;
    }

    // /**
    //  * Teacher Join Live Class
    //  * 
    //  * @param int $liveClassId
    //  * 
    //  * @return LiveUser|string
    //  */
    // public function joinLiveClass($liveClassId)
    // {
    //     $status = $this->liveClassService->isTeacherLiveClassNotStarted($liveClassId);
    //     if (gettype($status) == 'string') {
    //         return $status;
    //     }
    //     $status = $this->liveClassService->isTeacherLiveClassNotEnded($liveClassId);
    //     if (gettype($status) == 'string') {
    //         return $status;
    //     }
    //     return $this->userService->userJoinLiveClass($liveClassId);
    // }

    /**
     * Teacher leave Live Class
     * 
     * @param int $id
     * 
     * @return bool
     */
    public function leaveLiveClass($id)
    {
        // Do What ?
        // $liveClass = $this->liveClassService->getLiveClassById($id);
        // $userId = auth()->user()->id;
        // $data = [
        //     'user_id' => $userId,
        //     'live_class_id' => $liveClass->id
        // ];

        // return $this->liveUserService->leaveLiveTeacher($data);
    }
}
