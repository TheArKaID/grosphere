<?php

namespace App\Services;

use App\Models\Agency;

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
     * Create Agency
     * 
     * @param array $data
     * 
     * @return \App\Models\Agency
     */
    public function create($data)
    {
        $data['address'] = $data['address'] ?? "-";
        $data['phone'] = $data['phone'] ?? "-";
        $data['email'] = $data['email'] ?? "-";
        $data['website'] = $data['website'] ?? "-";
        $data['about'] = $data['about'] ?? "-";
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
}
