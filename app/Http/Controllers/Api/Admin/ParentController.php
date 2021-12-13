<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateParentRequest;
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
        
        $response = $parents->count() == 0 ? [] : $parents->response()->getData(true);
        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'response' => $response
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateParentRequest $request)
    {
        $validated = $request->validated();
        $parent = new ParentResource($this->parentService->createParent($validated));
        $response = $parent->count() == 0 ? [] : $parent->response()->getData(true);
        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'response' => $response
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
