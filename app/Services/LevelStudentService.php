<?php

namespace App\Services;

use App\Models\LevelStudent;

class LevelStudentService
{
    private $levelStudent;

    public function __construct(LevelStudent $levelStudent)
    {
        $this->levelStudent = $levelStudent;
    }

    /**
     * Get all levelStudents
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        if (request()->has('search')) {
            $search = request()->get('search');
            $this->levelStudent = $this->search($search);
        }
        if (request()->has('page') && request()->get('page') == 'all') {
            return $this->levelStudent->get();
        }
        return $this->levelStudent->paginate(request('size', 10));
    }

    /**
     * Get levelStudent by id
     * 
     * @param int $id
     * 
     * @return LevelStudent
     */
    public function getById($id)
    {
        return $this->levelStudent->findOrFail($id);
    }

    /**
     * Create levelStudent
     * 
     * @param array $data
     * 
     * @return LevelStudent
     */
    public function create(array $data)
    {
        return $this->levelStudent->create($data);
    }

    /**
     * Update levelStudent
     * 
     * @param int $id
     * @param array $data
     * 
     * @return LevelStudent
     */
    public function update($id, array $data)
    {
        $levelStudent = $this->levelStudent->findOrFail($id);
        $levelStudent->update($data);
        return $levelStudent;
    }

    /**
     * Delete levelStudent
     * 
     * @param int $id
     * 
     * @return bool
     */
    public function delete($id)
    {
        $levelStudent = $this->levelStudent->findOrFail($id);
        return $levelStudent->delete();
    }

    /**
     * Search LevelStudent
     * 
     * @param string $search
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function search($search)
    {
        return $this->levelStudent->where('status', '=', $search);
    }
}
