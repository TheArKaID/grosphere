<?php

namespace App\Http\Controllers\Api\Super;

use App\Exceptions\ModelGetEmptyException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdminRequest;
use App\Http\Requests\UpdateAdminRequest;
use App\Http\Resources\AdminResource;
use App\Services\AdminService;

class AdminController extends Controller
{
    protected $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $admins = AdminResource::collection($this->adminService->getAll());

        if (count($admins) == 0) {
            throw new ModelGetEmptyException('Admins');
        }

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $admins->response()->getData(true)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreAdminRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAdminRequest $request)
    {
        $validated = $request->validated();

        $admin = $this->adminService->create($validated);
        
        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => new AdminResource($admin)
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
        $admin = $this->adminService->getOne($id);

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => new AdminResource($admin)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateAdminRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAdminRequest $request, $id)
    {
        $validated = $request->validated();

        $admin = $this->adminService->update($id, $validated);

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => new AdminResource($admin)
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
        $this->adminService->delete($id);

        return response()->json([
            'status' => 200,
            'message' => 'Success'
        ], 200);
    }
}
