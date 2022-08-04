<?php

namespace App\Services;

use App\Models\Agency;
use Illuminate\Support\Facades\Storage;

class AgencyService
{
    private $agency;

    public function __construct(Agency $agency)
    {
        $this->agency = $agency;
    }

    /**
     * Get all Agencies
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        if (request()->has('search')) {
            $search = request()->get('search');
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
     * @param int $id
     * 
     * @return Agency
     */
    public function getOne($id)
    {
        return $this->agency->findOrFail($id);
    }

    /**
     * Get Agency Config by Agency Key
     * 
     * @param string $agencyKey
     * 
     * @return Agency
     */
    public function getConfig($agencyKey)
    {
        return $this->agency->select('name', 'logo', 'sub_title', 'color')->where('key', $agencyKey)->firstOrFail();
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
        return $this->agency->create($data);
    }

    /**
     * Update Agency
     * 
     * @param int $id
     * @param array $data
     * 
     * @return Agency
     */
    public function update($id, $data)
    {
        $agency = $this->getOne($id);
        $data['key'] = $data['key'] ?? $agency['key'];
        $data['address'] = $data['address'] ?? $agency['address'];
        $data['phone'] = $data['phone'] ?? $agency['phone'];
        $data['email'] = $data['email'] ?? $agency['email'];
        $data['website'] = $data['website'] ?? $agency['website'];
        $data['about'] = $data['about'] ?? $agency['about'];
        $agency->update($data);
        return $agency;
    }

    /**
     * Delete Agency
     * 
     * @param int $id
     * 
     * @return \App\Models\Agency
     */
    public function delete($id)
    {
        $agency = $this->getOne($id);
        return $agency->delete();
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
            ->where('name', 'like', '%' . $search . '%')
            ->orWhere('address', 'like', '%' . $search . '%')
            ->orWhere('email', 'like', '%' . $search . '%')
            ->orWhere('website', 'like', '%' . $search . '%');
    }

    /**
     * Get Agency of current user
     * 
     * @return \App\Models\Agency
     */
    public function getCurrentAgency()
    {
        return auth()->user()->agency;
    }

    /**
     * Update Agency of current user
     * 
     * @param array $data
     * 
     * @return \App\Models\Agency
     */
    public function updateCurrentAgency($data)
    {
        $agency = $this->getCurrentAgency();
        
        // remove null
        $data = array_filter($data, function ($value) {
            return $value !== null;
        });

        // Update logo
        if (isset($data['logo'])) {
            $this->updateLogo($agency->id, $data['logo']);
        }
        $agency->update($data);
        return $agency;
    }

    /**
     * Update logo of current agency
     * 
     * @param int $id
     * @param mixed $file
     * 
     * @return mixed
     */
    public function updateLogo($id, $file)
    {
        return Storage::cloud()->putFileAs(
            'agencies/' . $id,
            $file,
            'logo.png'
        );
    }
}
