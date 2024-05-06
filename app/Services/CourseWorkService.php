<?php

namespace App\Services;

use App\Models\CourseWork;
use Illuminate\Support\Facades\DB;

class CourseWorkService
{
    private $courseWork;

    public function __construct(CourseWork $courseWork)
    {
        $this->courseWork = $courseWork;
    }

    /**
     * Get all CourseWorks
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        if (request()->has('search')) {
            $search = request()->get('search');
            $this->courseWork = $this->search($search);
        }
        if (request()->has('page') && request()->get('page') == 'all') {
            return $this->courseWork->get();
        }
        return $this->courseWork->paginate(request('size', 10));
    }

    /**
     * Get One CourseWork
     * 
     * @param int $id
     * 
     * @return CourseWork
     */
    public function getOne($id)
    {
        return $this->courseWork->findOrFail($id);
    }

    /**
     * Create CourseWork
     * 
     * @param array $data
     * 
     * @return \App\Models\CourseWork
     */
    public function create($data)
    {
        return $this->courseWork->create($data);
    }

    /**
     * Update CourseWork
     * 
     * @param CourseWork $courseWork
     * @param array $data
     * 
     * @return CourseWork
     */
    public function update(CourseWork $courseWork, $data)
    {
        $courseWork->update($data);
        return $courseWork;
    }

    /**
     * Delete CourseWork
     * 
     * @param CourseWork $courseWork
     * 
     * @return \App\Models\CourseWork
     */
    public function delete(CourseWork $courseWork)
    {
        $courseWork->delete();
        return $courseWork;
    }

    /**
     * Search in CourseWork
     * 
     * @param string $search
     * @return CourseWork
     */
    public function search($search)
    {
        return $this->courseWork->where('subject', 'like', '%' . $search . '%')
        ->orWhere('grade', 'like', '%' . $search . '%')
        ->orWhere('term', 'like', '%' . $search . '%');
    }
}
