<?php

namespace App\Services;

use App\Models\Tutor;
use Illuminate\Support\Facades\DB;

class TutorService
{
    private $tutor, $userService, $liveClassService;

    public function __construct(
        Tutor $tutor,
        UserService $userService,
        LiveClassService $liveClassService
    ) {
        $this->tutor = $tutor;
        $this->userService = $userService;
        $this->liveClassService = $liveClassService;
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
        $tutor = $this->tutor->create($data);

        $user->assignRole('tutor');

        DB::commit();

        return $tutor;
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

        $tutor = $this->getById($id);

        $tutor->update($data);
        $tutor->user->update($data);

        DB::commit();

        return $tutor;
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

        $tutor = $this->getById($id);

        $tutor->delete();
        $this->userService->deleteUser($tutor->user_id);

        DB::commit();

        return true;
    }

    /**
     * Change Tutor Password
     * 
     * @param int $id
     * @param string $password
     * 
     * @return bool
     */
    public function changePassword(int $id, string $password)
    {
        $tutor = $this->getById($id);

        $this->userService->changePassword($tutor->user_id, $password);

        return true;
    }

    /**
     * Change Tutor's password By Tutor
     * 
     * @param int $id
     * @param array $data
     * 
	 * @return bool
     */
    public function changePasswordByTutor(int $id, array $data)
    {
        $tutor = $this->getById($id);

        $this->userService->changePassword($tutor->user_id, $data['new_password']);

        return true;
    }

    /**
     * Tutor Join Live Class
     * 
     * @param int $liveClassId
     * 
     * @return LiveUser
     */
    public function joinLiveClass($liveClassId)
    {
        if($this->liveClassService->isTutorLiveClassNotEnded($liveClassId)) {
            return $this->userService->userJoinLiveClass($liveClassId);
        }
        return false;
    }

    /**
     * Tutor leave Live Class
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

        // return $this->liveUserService->leaveLiveTutor($data);
    }
}
