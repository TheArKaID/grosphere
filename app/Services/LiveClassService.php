<?php

namespace App\Services;

use App\Models\LiveClass;
use Illuminate\Support\Facades\DB;

class LiveClassService
{
    private $liveClass, $classService;

    public function __construct(LiveClass $liveClass, ClassService $classService)
    {
        $this->liveClass = $liveClass;
        $this->classService = $classService;
    }

    /**
     * Get all live classes
     * 
     * @return Collection
     */
    public function getAllLiveClasses()
    {
        if (request()->has('search')) {
            $search = request()->get('search');
            $this->liveClass = $this->searchLiveClasses($search);
        }
        if (request()->has('page') && request()->get('page') == 'all') {
            return $this->liveClass->get();
        }
        return $this->liveClass->paginate(request('size', 10));
    }

    /**
     * Cretae Live Class
     * 
     * @param array $data
     * 
     * @return LiveClass
     */
    public function createLiveClass(array $data)
    {
        DB::beginTransaction();

        $class = $this->classService->createClass($data);
        $data['class_id'] = $class->id;
        $data['uniq_code'] = '';
        $liveClass = $this->liveClass->create($data);
        $liveClass->uniq_code = $this->generateUniqCode($liveClass->id);
        $liveClass->save();

        DB::commit();
        return $liveClass;
    }

    /**
     * Get Live Class
     * 
     * @param int $id
     * 
     * @return LiveClass
     */
    public function getLiveClassById($id)
    {
        return $this->liveClass->findOrFail($id);
    }

    /**
     * Update Live Class
     * 
     * @param int $id
     * @param array $data
     * 
     * @return LiveClass
     */
    public function updateLiveClass($id, array $data)
    {
        DB::beginTransaction();

        $liveClass = $this->getLiveClassById($id);
        $liveClass->update($data);

        $this->classService->updateClass($liveClass->class_id, $data);

        DB::commit();
        return $liveClass;
    }

    /**
     * Delete Live Class
     * 
     * @param int $id
     * 
     * @return bool
     */
    public function deleteLiveClass($id)
    {
        DB::beginTransaction();

        $liveClass = $this->getLiveClassById($id);
        $liveClass->delete();
        $this->classService->deleteClass($liveClass->class_id);

        DB::commit();
        return true;
    }

    /**
     * Generate Uniq Code with Class ID and 10 length
     * 
     * @param int $id
     * 
     * @return string
     */
    public function generateUniqCode(int $id)
    {
        return $id . substr(md5($id . rand(1, 100)), 0, 10 - strlen($id));
    }

    /**
     * Search in live classes
     * 
     * @param string $search
     * @return LiveClass
     */
    public function searchLiveClasses($search)
    {
        return $this->liveClass->whereHas('class', function ($class) use ($search) {
            $class->where('name', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%')->whereHas('tutor', function ($tutor) use ($search) {
                    $tutor->where('name', 'like', '%' . $search . '%');
                });
        });
    }

    /**
     * Get All Current Tutor Live Classes
     * 
     * @return Collection
     */
    public function getAllCurrentTutorLiveClasses()
    {
        $tutorId = auth()->user()->detail->id;

        $this->liveClass = $this->liveClass->whereHas('class', function ($class) use ($tutorId) {
            $class->where('tutor_id', $tutorId);
        });

        if (request()->has('search')) {
            $search = request()->get('search');
            $this->liveClass = $this->searchLiveClasses($search);
        }

        if (request()->has('page') && request()->get('page') == 'all') {
            return $this->liveClass->get();
        }

        return $this->liveClass->paginate(request('size', 10));
    }

    /**
     * Get Current Tutor Live Class
     * 
     * @param int $id
     * 
     * @return LiveClass
     */
    public function getCurrentTutorLiveClass($id)
    {
        $tutorId = auth()->user()->detail->id;

        return $this->liveClass->whereHas('class', function ($class) use ($tutorId) {
            $class->where('tutor_id', $tutorId);
        })->find($id);
    }

    /**
     * Update Current Tutor Live Class
     * 
     * @param int $id
     * @param array $data
     * 
     * @return LiveClass
     */
    public function updateCurrentTutorLiveClass($id, array $data)
    {
        DB::beginTransaction();

        $liveClass = $this->getCurrentTutorLiveClass($id);
        $liveClass->update($data);

        $this->classService->updateClass($liveClass->class_id, $data);

        DB::commit();
        return $liveClass;
    }
}
