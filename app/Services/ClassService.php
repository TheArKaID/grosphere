<?php

namespace App\Services;

use App\Models\Classes;
use Illuminate\Support\Facades\Storage;

class ClassService
{
    private $class;

    public function __construct(Classes $class)
    {
        $this->class = $class;
    }

    /**
     * Get By ID
     * 
     * @param int $id
     * 
     * @return Classes
     */
    public function getClassById(int $id)
    {
        return $this->class->findOrFail($id);
    }

    /**
     * Create Class
     * 
     * @param array $data
     * 
     * @return Class
     */
    public function createClass(array $data)
    {
        $data['type'] = Classes::$LIVE;
        $class = $this->class->create($data);

        // if($data['thumbnail']) {
        //     Storage::cloud()->put('class/' . $class->id . '/thumbnail', $data['thumbnail']);
        //     $data['thumbnail']->storeAs('classes/' . $class->id, 'thumbnail.jpg', 'public');
        // }

        return $class;
    }

    /**
     * Update Class
     * 
     * @param int $id
     * @param array $data
     * 
     * @return Classes
     */
    public function updateClass(int $id, array $data)
    {
        $class = $this->getClassById($id);
        $class->update($data);
        return $class;
    }

    /**
     * Delete Class
     * 
     * @param int $id
     * 
     * @return bool
     */
    public function deleteClass(int $id)
    {
        $class = $this->getClassById($id);
        return $class->delete();
    }
}
