<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\AnnouncementResource;
use App\Services\AnnouncementService;

class AnnouncementController extends Controller
{
    protected $annoucementService;

    public function __construct(AnnouncementService $annoucementService)
    {
        $this->annoucementService = $annoucementService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $announcements = AnnouncementResource::collection($this->annoucementService->getAllForUser());

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $announcements
        ], 200);
    }

    /**
     * @param int $annoucementid
     * 
     * @return \Illuminate\Http\Response
     */
    public function show($annoucementid)
    {
        $announcements = new AnnouncementResource($this->annoucementService->getByIdForUser($annoucementid));

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $announcements
        ], 200);
    }
}
