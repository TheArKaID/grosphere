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

        if (isset($data['thumbnail'])) {
            $fileName = $class->id . '-' . time() . '.png';
            Storage::cloud()->putFileAs('class/thumbnail', $data['thumbnail'], $fileName);
            $class->thumbnail = $fileName;
            $class->save();
        }

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

        $class->update([
            'name' => $data['name'],
            'description' => $data['description'],
            'start_time' => $data['start_time'],
            'duration' => $data['duration']
        ]);

        if (isset($data['thumbnail'])) {
            if (Storage::cloud()->exists('class/thumbnail/' . $class->thumbnail)) {
                Storage::cloud()->delete('class/thumbnail/' . $class->thumbnail);
            }
            $fileName = $class->id . '-' . time() . '.png';
            Storage::cloud()->putFileAs('class/thumbnail', $data['thumbnail'], $fileName);
            $class->thumbnail = $fileName;
            $class->save();
        }

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

        if (Storage::cloud()->exists('class/thumbnail/' . $class->thumbnail)) {
            Storage::cloud()->delete('class/thumbnail/' . $class->thumbnail);
        }

        return $class->delete();
    }
}
