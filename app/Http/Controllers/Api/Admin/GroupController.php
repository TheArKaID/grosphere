<?php

namespace App\Http\Controllers\Api\Admin;

use App\Exceptions\ModelGetEmptyException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGroupRequest;
use App\Http\Requests\UpdateGroupRequest;
use App\Http\Resources\GroupResource;
use App\Services\GroupService;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    protected $groupService;

    public function __construct(GroupService $groupService)
    {
        $this->groupService = $groupService;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $groups = $this->groupService->getAll();
        
        if ($groups->count() == 0) {
            throw new ModelGetEmptyException("Group");
        }

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => GroupResource::collection($groups)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreGroupRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreGroupRequest $request)
    {
        $group = $this->groupService->create($request->validated());

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => new GroupResource($group)
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
        $group = $this->groupService->getOne($id);

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => new GroupResource($group)
        ], 200);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateGroupRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateGroupRequest $request, int $id)
    {
        $group = $this->groupService->update($id, $request->validated());

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => new GroupResource($group)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $this->groupService->delete($id);

        return response()->json([
            'status' => 200,
            'message' => 'Success'
        ], 200);
    }

    /**
     * Add Student to the Group
     * 
     * @param Request $request
     * @param int $id
     * 
     * @return \Illuminate\Http\Response
     */
    public function addStudent(Request $request, int $id)
    {
        $group = $this->groupService->addStudent($id, $request->student_ids);

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => new GroupResource($group)
        ], 200);
    }
    
    /**
     * Remove Student from the Group
     * 
     * @param Request $request
     * @param int $id
     * 
     * @return \Illuminate\Http\Response
     */
    public function removeStudent(Request $request, int $id)
    {
        $group = $this->groupService->removeStudent($id, $request->student_ids);

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => new GroupResource($group)
        ], 200);
    }
}
