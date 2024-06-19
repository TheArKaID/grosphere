<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Exceptions\ModelGetEmptyException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClassSessionRequest;
use App\Http\Requests\UpdateClassSessionRequest;
use App\Http\Resources\ClassSessionResource;
use App\Models\ClassSession;
use App\Services\ClassSessionService;
use Illuminate\Http\Request;

class ClassSessionController extends Controller
{
    protected $classSessionService;

    public function __construct(ClassSessionService $classSessionService)
    {
        $this->classSessionService = $classSessionService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $classSessions = ClassSessionResource::collection($this->classSessionService->getAll());

        if ($classSessions->count() == 0) {
            throw new ModelGetEmptyException('ClassSession');
        }

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $classSessions->response()->getData(true)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClassSessionRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $classSession = new ClassSessionResource($this->classSessionService->getOne($id));

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $classSession->response()->getData(true)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClassSessionRequest $request, ClassSession $classSession)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClassSession $classSession)
    {
        //
    }

    /**
     * End the class session.
     * 
     * @param \Illuminate\Http\Request $request
     * @param string $id
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function end(Request $request, string $id)
    {
        $data = $request->validate([
            'summary' => 'required|string',
            'students' => 'required|array',
            'students.*.id' => 'required|integer|exists:students,id',
            'students.*.rating' => 'required|in:1,2,3,4,5',
            'students.*.remark' => 'required|string'
        ]);
        $this->classSessionService->end($id, $data);

        return response()->json([
            'status' => 200,
            'message' => 'Success'
        ], 200);
    }
}
