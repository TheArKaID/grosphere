<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTeacherFileRequest;
use App\Http\Requests\UpdateTeacherFileRequest;
use App\Models\TeacherFile;
use App\Services\TeacherService;

class TeacherFileController extends Controller
{
    protected $teacherService;

    public function __construct(TeacherService $teacherService)
    {
        $this->teacherService = $teacherService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'status' => 200,
            'message' => "Success",
            'data' => [
                'meta' => [
                    'max_size' => $this->teacherService->getMaxFileSizeMb(),
                    'used_size' => $this->teacherService->getTotalFileSizeMb(auth()->user()->detail->id)
                ],
                'files' => $this->teacherService->getAllTeacherFile(auth()->user()->detail->id)
            ]
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTeacherFileRequest $request)
    {
        $validated = $request->validated();
        $teacherFile = $this->teacherService->createTeacherFile(auth()->user()->detail->id, $validated,);

        return response()->json([
            'status' => 200,
            'message' => 'Teacher File Created Successfully',
            'data' => $teacherFile
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(TeacherFile $teacherFile)
    {
        return response()->json([
            'status' => 200,
            'message' => "Success",
            'data' => $teacherFile
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTeacherFileRequest $request, TeacherFile $teacherFile)
    {
        $validated = $request->validated();
        $teacherFile = $this->teacherService->updateTeacherFile($teacherFile, $validated);

        return response()->json([
            'status' => 200,
            'message' => 'Teacher File Updated Successfully',
            'data' => $teacherFile
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TeacherFile $storage_file)
    {
        $this->teacherService->deleteTeacherFile($storage_file);

        return response()->json([
            'status' => 200,
            'message' => 'Teacher File Deleted Successfully'
        ], 200);
    }
}
