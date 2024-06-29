<?php

namespace App\Services;

use App\Models\Curriculum;
use Illuminate\Support\Facades\DB;

class CurriculumService
{
    private $curriculum;

    public function __construct(Curriculum $curriculum)
    {
        $this->curriculum = $curriculum;
    }

    /**
     * Get all Curriculums
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        if ($search = request()->get('search')) {
            $this->curriculum = $this->search($search);
        }
        if (request()->has('page') && request()->get('page') == 'all') {
            return $this->curriculum->get();
        }
        return $this->curriculum->paginate(request('size', 10));
    }

    /**
     * Get One Curriculum
     * 
     * @param string $id
     * 
     * @return Curriculum
     */
    public function getOne($id)
    {
        return $this->curriculum->findOrFail($id);
    }

    /**
     * Create Curriculum
     * 
     * @param array $data
     * 
     * @return \App\Models\Curriculum
     */
    public function create($data)
    {
		$data['agency_id'] = auth()->user()->agency_id;
        return $this->curriculum->create($data);
    }

    /**
     * Update Curriculum
     * 
     * @param Curriculum $curriculum
     * @param array $data
     * 
     * @return Curriculum
     */
    public function update(Curriculum $curriculum, $data)
    {
        $curriculum->update($data);
        return $curriculum;
    }

    /**
     * Delete Curriculum
     * 
     * @param Curriculum $curriculum
     * 
     * @return \App\Models\Curriculum
     */
    public function delete(Curriculum $curriculum)
    {
        $curriculum->delete();
        return $curriculum;
    }

    /**
     * Search in Curriculum
     * 
     * @param string $search
     * @return Curriculum
     */
    public function search($search)
    {
        return $this->curriculum->where('subject', 'like', '%' . $search . '%')
        ->orWhere('grade', 'like', '%' . $search . '%')
        ->orWhere('term', 'like', '%' . $search . '%');
    }
}
