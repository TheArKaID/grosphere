<?php

namespace App\Services;

use App\Models\Level;

class LevelService
{
    private $level;

    public function __construct(Level $level)
    {
        $this->level = $level;
    }

    /**
     * Get all levels
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        if (request()->has('search')) {
            $search = request()->get('search');
            $this->level = $this->search($search);
        }
        if (request()->has('page') && request()->get('page') == 'all') {
            return $this->level->get();
        }
        return $this->level->paginate(request('size', 10));
    }

    /**
     * Get level by id
     * 
     * @param int $id
     * 
     * @return Level
     */
    public function getById($id)
    {
        return $this->level->findOrFail($id);
    }

    /**
     * Create level
     * 
     * @param array $data
     * 
     * @return Level
     */
    public function create(array $data)
    {
        return $this->level->create($data);
    }

    /**
     * Update level
     * 
     * @param int $id
     * @param array $data
     * 
     * @return Level
     */
    public function update($id, array $data)
    {
        $level = $this->level->findOrFail($id);
        $level->update($data);
        return $level;
    }

    /**
     * Delete level
     * 
     * @param int $id
     * 
     * @return bool
     */
    public function delete($id)
    {
        $level = $this->level->findOrFail($id);
        return $level->delete();
    }

    /**
     * Search Level
     * 
     * @param string $search
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function search($search)
    {
        return $this->level->where('name', 'like', '%' . $search . '%');
    }
}
