<?php

namespace App\Http\Controllers\Api\User;

use App\Exceptions\ModelGetEmptyException;
use App\Http\Controllers\Controller;
use App\Http\Resources\AnnouncementResource;
use App\Services\AnnouncementService;

class AnnouncementController extends Controller
{
    public function __construct(
        protected AnnouncementService $service
    ) {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $announcements = AnnouncementResource::collection($this->service->getAll());

        if($announcements->count() == 0) {
            // throw new ModelGetEmptyException("Announcements");
        }

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $announcements
        ], 200);
    }

    /**
     * @param int $announcementid
     * 
     * @return \Illuminate\Http\Response
     */
    public function show($announcementid)
    {
        $announcements = new AnnouncementResource($this->service->getOne($announcementid));

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $announcements
        ], 200);
    }
}
