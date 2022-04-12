<?php

namespace App\Http\Controllers\Api\Tutor;

use App\Exceptions\ModelGetEmptyException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreChapterMaterialRequest;
use App\Http\Requests\UpdateChapterMaterialRequest;
use App\Http\Resources\ChapterMaterialResource;
use App\Models\ChapterMaterial;
use App\Services\ChapterMaterialService;
use Illuminate\Support\Facades\Auth;

class ChapterMaterialController extends Controller
{
    private $chapterMaterialService;

    public function __construct(
        ChapterMaterialService $chapterMaterialService
    ) {
        $this->chapterMaterialService = $chapterMaterialService;
    }

    /**
     * Display a listing of the resource.
     * 
     * @param int $courseWorkId
     * @param int $courseChapterId
     *
     * @return \Illuminate\Http\Response
     */
    public function index($courseWorkId, $courseChapterId)
    {
        $chapterMaterials = ChapterMaterialResource::collection($this->chapterMaterialService->getAll($courseChapterId, Auth::user()->detail->id));

        if (count($chapterMaterials) == 0) {
            throw new ModelGetEmptyException('Chapter Materials');
        }

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $chapterMaterials->response()->getData(true)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreChapterMaterialRequest  $request
     * @param int $courseWorkId
     * @param int $courseChapterId
     * 
     * @return \Illuminate\Http\Response
     */
    public function store(StoreChapterMaterialRequest $request, $courseWorkId, $courseChapterId)
    {
        $validated = $request->validated();

        $validated['course_work_id'] = $courseWorkId;
        $validated['course_chapter_id'] = $courseChapterId;
        $validated['tutor_id'] = Auth::user()->detail->id;

        $chapterMaterial = $this->chapterMaterialService->create($validated);

        return response()->json([
            'status' => 200,
            'message' => 'Chapter Material uploaded successfully',
            'data' => new ChapterMaterialResource($chapterMaterial)
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param int $courseWorkId
     * @param int $courseChapterId
     * @param  int  $chapterMaterialId
     * @return \Illuminate\Http\Response
     */
    public function show(int $courseWorkId, int $courseChapterId, int $chapterMaterialId)
    {
        $chapterMaterial = $this->chapterMaterialService->getById($courseChapterId, $chapterMaterialId, Auth::user()->detail->id);

        return response()->json([
            'status' => 200,
            'message' => 'Chapter Material retrieved successfully',
            'data' => new ChapterMaterialResource($chapterMaterial)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $courseWorkId
     * @param int $courseChapterId
     * @param  int  $chapterMaterialId
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $courseWorkId, int $courseChapterId, int $chapterMaterialId)
    {
        $this->chapterMaterialService->delete($courseChapterId, $chapterMaterialId, Auth::user()->detail->id);

        return response()->json([
            'status' => 200,
            'message' => 'Chapter Material deleted successfully'
        ], 200);
    }
}
