<?php

namespace App\Services;

use App\Models\Admin;
use Illuminate\Support\Facades\DB;

class AdminService
{
    private $admin, $userService;

    public function __construct(Admin $admin, UserService $userService)
    {
        $this->admin = $admin;
        $this->userService = $userService;
    }

    /**
     * Get all Admins
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        if (request()->has('search')) {
            $search = request()->get('search');
            $this->admin = $this->search($search);
        }
        if (request()->has('page') && request()->get('page') == 'all') {
            return $this->admin->get();
        }
        return $this->admin->paginate(request('size', 10));
    }

    /**
     * Get One Admin
     * 
     * @param int $id
     * 
     * @return Admin
     */
    public function getOne($id)
    {
        return $this->admin->findOrFail($id);
    }

    /**
     * Create Admin
     * 
     * @param array $data
     * 
     * @return \App\Models\Admin
     */
    public function create($data)
    {
        DB::beginTransaction();
        $data['password'] = bcrypt($data['password']);

        $user = $this->userService->createUser($data);
        $user->assignRole('admin');
        $data['user_id'] = $user->id;

        $admin = $this->admin->create($data);

        DB::commit();
        return $admin;
    }

    /**
     * Update Admin
     * 
     * @param int $id
     * @param array $data
     * 
     * @return Admin
     */
    public function update($id, $data)
    {
        DB::beginTransaction();

        $admin = $this->admin->findOrFail($id);
        $this->userService->updateUser($admin->user_id, $data);

        DB::commit();
        return $admin;
    }

    /**
     * Delete Admin
     * 
     * @param int $id
     * 
     * @return \App\Models\Admin
     */
    public function delete($id)
    {
        DB::beginTransaction();
        $admin = $this->getOne($id);
        $admin->delete();
        $this->userService->deleteUser($admin->user_id);

        DB::commit();
        return true;
    }

    /**
     * Search in Admin
     * 
     * @param string $search
     * @return Admin
     */
    public function search($search)
    {
        return $this->admin
            ->whereHas('user', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->whereHas('agency', function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%')
                            ->orWhere('address', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%')
                            ->orWhere('website', 'like', '%' . $search . '%');
                    });
            });
    }
}
