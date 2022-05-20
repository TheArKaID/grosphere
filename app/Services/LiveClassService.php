<?php

namespace App\Services;

use App\Models\Classes;
use App\Models\LiveClass;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LiveClassService
{
    private $liveClass, $classService, $liveUserService;

    public function __construct(
        LiveClass $liveClass,
        ClassService $classService,
        LiveUserService $liveUserService
    ) {
        $this->liveClass = $liveClass;
        $this->classService = $classService;
        $this->liveUserService = $liveUserService;
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
        if(request()->has('range')) {
            $range = request()->get('range');
            switch ($range) {
                case 'today':
                    $this->liveClass = $this->liveClass->whereDate('start_time', Carbon::today());
                    break;
                case 'this-week':
                    $this->liveClass = $this->liveClass->whereBetween('start_time', [Carbon::now(), Carbon::now()->addWeek()]);
                    break;
                case 'next-week':
                    $this->liveClass = $this->liveClass->whereBetween('start_time', [Carbon::now()->addWeek(), Carbon::now()->addWeeks(2)]);
                    break;
                default:
                    $this->liveClass = $this->liveClass->whereDate('start_time', Carbon::today());
                    break;
            }
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

        $data['type'] = Classes::$LIVE;
        $class = $this->classService->createClass($data);
        $data['class_id'] = $class->id;
        $liveClass = $this->liveClass->create($data);

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
        })->findOrFail($id);
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

    /**
     * Delete Current Tutor Live Class
     * 
     * @param int $id
     * 
     * @return bool
     */
    public function deleteCurrentTutorLiveClass($id)
    {
        DB::beginTransaction();

        $liveClass = $this->getCurrentTutorLiveClass($id);
        $liveClass->delete();
        $this->classService->deleteClass($liveClass->class_id);

        DB::commit();
        return true;
    }

    /**
     * Check if Live Class is started
     * 
     * @param int $liveClassId
     * 
     * @return bool
     */
    public function isLiveClassStarted($liveClassId)
    {
        $liveClass = $this->getLiveClassById($liveClassId);
        $liveClassStartTime = Carbon::parse($liveClass->start_time);
        $liveClassEndTime = Carbon::parse($liveClass->start_time)->addMinutes($liveClass->duration);
        $currentTime = Carbon::now();

        return $currentTime->between($liveClassStartTime, $liveClassEndTime);
    }

    /**
     * Is Tutor Live Class not ended
     * 
     * @param int $liveClassId
     * 
     * @return bool
     */
    public function isTutorLiveClassNotEnded($liveClassId)
    {
        $liveClass = $this->getCurrentTutorLiveClass($liveClassId);
        $liveClassEndTime = Carbon::parse($liveClass->start_time)->addMinutes($liveClass->duration);
        $currentTime = Carbon::now();

        return $currentTime->lt($liveClassEndTime) ? true : 'Live Class has ended';
    }

    /**
     * Is Tutor Live Class not Started
     * 
     * @param int $liveClassId
     * 
     * @return bool
     */
    public function isTutorLiveClassNotStarted($liveClassId)
    {
        $liveClass = $this->getCurrentTutorLiveClass($liveClassId);
        $liveClassStartTime = Carbon::parse($liveClass->start_time);
        $currentTime = Carbon::now();

        return $currentTime->lt($liveClassStartTime) ? 'Live Class has not started' : false;
    }
}
