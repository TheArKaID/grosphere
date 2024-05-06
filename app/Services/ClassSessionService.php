<?php

namespace App\Services;

use App\Models\ClassSession;
use Illuminate\Support\Facades\DB;

class ClassSessionService
{
    private $classSession;

    public function __construct(ClassSession $classSession)
    {
        $this->classSession = $classSession;
    }

    /**
     * Get all ClassSessions
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        if (request()->has('search')) {
            $search = request()->get('search');
            $this->classSession = $this->search($search);
        }
        if (request()->has('page') && request()->get('page') == 'all') {
            return $this->classSession->get();
        }
        return $this->classSession->paginate(request('size', 10));
    }

    /**
     * Get One ClassSession
     * 
     * @param int $id
     * 
     * @return ClassSession
     */
    public function getOne($id)
    {
        return $this->classSession->findOrFail($id);
    }

    /**
     * Create ClassSession
     * 
     * @param array $data
     * 
     * @return \App\Models\ClassSession||\App\Models\ClassSession[]
     */
    public function create($data)
    {
        if (isset($data['total_session'])) {
            return DB::transaction(function () use ($data) {
                $totalSession = $data['total_session'];
                unset($data['total_session']);
                $classSessions = [];
                for ($i = 0; $i < $totalSession; $i++) {
                    $classSessions[] = $this->classSession->create($data);
                }
                return $classSessions;
            });
        } else {
            return $this->classSession->create($data);
        }
    }

    /**
     * Update ClassSession
     * 
     * @param ClassSession $classSession
     * @param array $data
     * 
     * @return ClassSession
     */
    public function update(ClassSession $classSession, $data)
    {
        $classSession->update($data);
        return $classSession;
    }

    /**
     * Delete ClassSession
     * 
     * @param ClassSession $classSession
     * 
     * @return \App\Models\ClassSession
     */
    public function delete(ClassSession $classSession)
    {
        $classSession->delete();
        return $classSession;
    }

    /**
     * Search in ClassSession
     * 
     * @param string $search
     * @return ClassSession
     */
    public function search($search)
    {
        return $this->classSession->where('title', 'like', '%' . $search . '%')
        ->orWhere('description', 'like', '%' . $search . '%')
        ->orWhere('date', 'like', '%' . $search . '%')
        ->orWhere('time', 'like', '%' . $search . '%');
    }
}
