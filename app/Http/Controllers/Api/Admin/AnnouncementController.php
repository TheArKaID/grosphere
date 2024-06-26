<?php

namespace App\Http\Controllers\Api\Admin;

use App\Exceptions\ModelGetEmptyException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AnnouncementRequest;
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

        if ($announcements->count() == 0) {
            throw new ModelGetEmptyException("Announcements");
        }

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $announcements->response()->getData(true)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  AnnouncementRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AnnouncementRequest $request)
    {
        $validated = $request->validated();

        $announcement = new AnnouncementResource($this->service->create($validated));

        return response()->json([
            'status' => 200,
            'message' => 'Announcement Created Successfully',
            'data' => $announcement
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $announcement = new AnnouncementResource($this->service->getOne($id));

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $announcement
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  AnnouncementRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AnnouncementRequest $request, $id)
    {
        $validated = $request->validated();

        $announcement = new AnnouncementResource($this->service->update($id, $validated));

        return response()->json([
            'status' => 200,
            'message' => 'Announcement Updated Successfully',
            'data' => $announcement
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->service->delete($id);

        return response()->json([
            'status' => 200,
            'message' => 'Announcement Deleted Successfully'
        ], 200);
    }

    /**
     * Toggle the status of the specified resource.
     * 
     * @param string $announcement_id
     * 
     * @return \Illuminate\Http\Response
     */
    public function toggleStatus($announcement_id)
    {
        $announcement = new AnnouncementResource($this->service->toggleStatus($announcement_id));

        return response()->json([
            'status' => 200,
            'message' => 'Announcement Status Toggled Successfully',
            'data' => $announcement
        ], 200);
    }
}
