<?php

namespace App\Services;

use App\Models\Announcement;

class AnnouncementService
{
    private $annoucement;

    public function __construct(Announcement $annoucement)
    {
        $this->annoucement = $annoucement;
    }

    /**
     * Get all announcements
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        if (request()->has('search')) {
            $search = request()->get('search');
            $this->annoucement = $this->search($search);
        }
        if (request()->has('page') && request()->get('page') == 'all') {
            return $this->annoucement->get();
        }
        return $this->annoucement->paginate(request('size', 10));
    }

    /**
     * Get announcement by id
     * 
     * @param int $id
     * 
     * @return Announcement
     */
    public function getById($id)
    {
        return $this->annoucement->findOrFail($id);
    }

    /**
     * Create announcement
     * 
     * @param array $data
     * 
     * @return Announcement
     */
    public function create(array $data)
    {
        return $this->annoucement->create($data);
    }

    /**
     * Update announcement
     * 
     * @param int $id
     * @param array $data
     * 
     * @return Announcement
     */
    public function update($id, array $data)
    {
        $announcement = $this->annoucement->findOrFail($id);
        $announcement->update($data);
        return $announcement;
    }

    /**
     * Delete announcement
     * 
     * @param int $id
     * 
     * @return bool
     */
    public function delete($id)
    {
        $announcement = $this->annoucement->findOrFail($id);
        return $announcement->delete();
    }

    /**
     * Search Announcement
     * 
     * @param string $search
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function search($search)
    {
        return $this->annoucement->where('name', 'like', '%' . $search . '%')
            ->orWhere('message', 'like', '%' . $search . '%');
    }
}
