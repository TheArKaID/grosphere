<?php

namespace App\Services;

use App\Models\Parents;
use Illuminate\Support\Facades\DB;

class ParentService
{
    private $parents;
    private $userService;

    public function __construct(
        Parents $parents,
        UserService $userService
    ) {
        $this->parents = $parents;
        $this->userService = $userService;
    }

    /**
     * Get All Parents
     * 
     * @return Parents
     */
    public function getAll()
    {
        if (request()->has('page') && request()->get('page') == 'all') {
            if (request()->has('search')) {
                $this->parents = $this->parents->whereHas('user', function ($query) {
                    $query->where('name', 'like', '%' . request()->get('search') . '%')
                        ->orWhere('email', 'like', '%' . request()->get('search') . '%')
                        ->orWhere('phone', 'like', '%' . request()->get('search') . '%');
                });
            }
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
}
