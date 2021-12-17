<?php

namespace App\Http\Controllers\Api\Admin;

use App\Exceptions\ModelGetEmptyException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreParentRequest;
use App\Http\Requests\UpdateParentRequest;
use App\Http\Resources\ParentResource;
use App\Services\ParentService;
use Illuminate\Http\Request;

class ParentController extends Controller
{
    private $parentService;

    public function __construct(ParentService $parentService)
    {
        $this->parentService = $parentService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $parents = ParentResource::collection($this->parentService->getAll());

        if ($parents->count() == 0) {
            throw new ModelGetEmptyException("Parent");
        }

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'response' => $parents->response()->getData(true)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreParentRequest $request)
    {
        $validated = $request->validated();
        $parent = new ParentResource($this->parentService->create($validated));

        return response()->json([
            'status' => 200,
            'message' => 'Parent Created Successfully',
            'response' => $parent
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
        $parent = $this->parentService->getById($id);

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'response' => new ParentResource($parent)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateParentRequest $request, $id)
    {
        $validated = $request->validated();
        $parent = new ParentResource($this->parentService->update($id, $validated));

        return response()->json([
            'status' => 200,
            'message' => 'Parent Updated Successfully',
            'response' => $parent
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
        $this->parentService->delete($id);

        return response()->json([
            'status' => 200,
            'message' => 'Parent Deleted Successfully',
        ], 200);
    }

    /**
     * Add a child to a parent
     * 
     * @param Request $request
     * @param int $parent_id
     * 
     * @return \Illuminate\Http\Response
     */
    public function addChild(Request $request, $parent_id)
    {
        $validated = $request->validate([
            'child_id' => 'required|integer'
        ]);

        $this->parentService->addChild($parent_id, $validated['child_id']);

        return response()->json([
            'status' => 200,
            'message' => 'Child Added Successfully'
        ], 200);
    }
}
