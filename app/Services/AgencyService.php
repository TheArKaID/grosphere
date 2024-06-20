<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\Agency;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AgencyService
{
    private $agency;

    public function __construct(Agency $agency)
    {
        $this->agency = $agency;
    }

    /**
     * Get all Agencys
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        if ($search = request()->get('search')) {
            $this->agency = $this->search($search);
        }
        if (request()->has('page') && request()->get('page') == 'all') {
            return $this->agency->get();
        }
        return $this->agency->paginate(request('size', 10));
    }

    /**
     * Get One Agency
     * 
     * @param string $id
     * 
     * @return Agency
     */
    public function getOne($id)
    {
        return $this->agency->findOrFail($id);
    }

    /**
     * Create Agency
     * 
     * @param array $data
     * 
     * @return \App\Models\Agency
     */
    public function create($data)
    {
        DB::beginTransaction();

        $agency = $this->agency->create($data);

        if ($logo = $data['logo'] ?? false) {
            $logo = base64_decode(substr($logo, strpos($logo, ",")+1));
            Storage::disk('s3')->put('agencies/' . $agency->id . '.png', $logo);
        }

        if ($smallLogo = $data['logo_sm'] ?? false) {
            $smallLogo = base64_decode(substr($smallLogo, strpos($smallLogo, ",")+1));
            Storage::disk('s3')->put('agencies/' . $agency->id . '-sm.png', $smallLogo);
        }

        DB::commit();
        return $agency;
    }

    /**
     * Update Agency
     * 
     * @param string $id
     * @param array $data
     * 
     * @return Agency
     */
    public function update($id, $data)
    {
        DB::beginTransaction();

        // Remove null values
        $data = array_filter($data, function ($value) {
            return $value !== null;
        });
        $agency = $this->getOne($id);
        $agency->update($data);

        if ($logo = $data['logo'] ?? false) {
            $logo  = base64_decode(substr($logo , strpos($logo , ",")+1));
            Storage::disk('s3')->put('agencies/' . $agency->id . '.png', $logo );
        }

        if ($smallLogo = $data['logo_sm'] ?? false) {
            $smallLogo = base64_decode(substr($smallLogo, strpos($smallLogo, ",")+1));
            Storage::disk('s3')->put('agencies/' . $agency->id . '-sm.png', $smallLogo);
        }

        DB::commit();
        return $agency;
    }

    /**
     * Delete Agency
     * 
     * @param string $id
     * 
     * @return \App\Models\Agency
     */
    public function delete($id)
    {
        DB::beginTransaction();
        $agency = $this->getOne($id);
        $agency->delete();

        DB::commit();
        return true;
    }

    /**
     * Search in Agency
     * 
     * @param string $search
     * @return Agency
     */
    public function search($search)
    {
        return $this->agency
        ->where(function (Builder $query) use ($search) {
            $query->orWhere('name', 'like', '%' . $search . '%');
        });
    }

    /**
     * Create Admin User
     * 
     * @param Agency $agency
     * @param array $data
     * 
     * @return \App\Models\User
     */
    public function createAdmin(Agency $agency, array $data)
    {
        DB::beginTransaction();
        $cred = isset($data['email']) ? 'email' : 'username';
        $user = $agency->users()->create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            $cred => $data[$cred],
            'agency_id' => $agency->id,
            'password' => bcrypt($data['password'])
        ]);

        $user->assignRole('admin');

        $admin = Admin::create([
            'user_id' => $user->id
        ]);

        $data['photo'] = base64_decode(substr($data['photo'], strpos($data['photo'], ",")+1));
        Storage::disk('s3')->put('users/' . $admin->id . '.png', $data['photo']);

        DB::commit();

        return $user;
    }

    /**
     * Delete Admin User
     * 
     * @param Agency $agency
     * @param string $adminId
     * 
     * @return void
     */
    public function deleteAdmin(Agency $agency, string $adminId)
    {
        $admin = Admin::findOrFail($adminId);
        if ($admin->user->hasRole('admin') && $admin->user->agency_id == $agency->id) {
            $admin->user->delete();
        }
        // Failed to delete admin
        else {
            
        }
    }
}
