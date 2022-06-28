<?php

namespace App\Services;

use App\Models\Institute;

class InstituteService
{
    private $institute;

    public function __construct(Institute $institute)
    {
        $this->institute = $institute;
    }

    /**
     * Get all Institutes
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        if (request()->has('search')) {
            $search = request()->get('search');
            $this->institute = $this->search($search);
        }
        if (request()->has('page') && request()->get('page') == 'all') {
            return $this->institute->get();
        }
        return $this->institute->paginate(request('size', 10));
    }

    /**
     * Get One Institute
     * 
     * @param int $id
     * 
     * @return Institute
     */
    public function getOne($id)
    {
        return $this->institute->findOrFail($id);
    }

    /**
     * Create Institute
     * 
     * @param array $data
     * 
     * @return \App\Models\Institute
     */
    public function create($data)
    {
        $data['address'] = $data['address'] ?? "-";
        $data['phone'] = $data['phone'] ?? "-";
        $data['email'] = $data['email'] ?? "-";
        $data['website'] = $data['website'] ?? "-";
        $data['about'] = $data['about'] ?? "-";
        return Institute::create($data);
    }

    /**
     * Update Institute
     * 
     * @param int $id
     * @param array $data
     * 
     * @return Institute
     */
    public function update($id, $data)
    {
        $institute = $this->getOne($id);
        $data['address'] = $data['address'] ?? $institute['address'];
        $data['phone'] = $data['phone'] ?? $institute['phone'];
        $data['email'] = $data['email'] ?? $institute['email'];
        $data['website'] = $data['website'] ?? $institute['website'];
        $data['about'] = $data['about'] ?? $institute['about'];
        $institute->update($data);
        return $institute;
    }

    /**
     * Delete Institute
     * 
     * @param int $id
     * 
     * @return \App\Models\Institute
     */
    public function delete($id)
    {
        $institute = $this->getOne($id);
        return $institute->delete();
    }

    /**
     * Search in Institute
     * 
     * @param string $search
     * @return Institute
     */
    public function search($search)
    {
        return $this->institute
            ->where('name', 'like', '%' . $search . '%')
            ->orWhere('address', 'like', '%' . $search . '%')
            ->orWhere('email', 'like', '%' . $search . '%')
            ->orWhere('website', 'like', '%' . $search . '%');
    }
}
