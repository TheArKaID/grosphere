<?php

namespace App\Http\Controllers\Api\Student;

use App\Exceptions\ModelGetEmptyException;
use App\Http\Controllers\Controller;
use App\Http\Resources\ChapterMaterialResource;
use App\Services\ChapterMaterialService;
use Illuminate\Support\Facades\Auth;

class ChapterMaterialController extends Controller
{
    private $service;

    public function __construct(ChapterMaterialService $service)
    {
        $this->service = $service;
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
        $chapterMaterials = ChapterMaterialResource::collection($this->service->getAll($courseChapterId));

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
     * Display the specified resource.
     *
     * @param int $courseWorkId
     * @param int $courseChapterId
     * @param  int  $chapterMaterialId
     * 
     * @return \Illuminate\Http\Response
     */
    public function show($courseWorkId, $courseChapterId, int $chapterMaterialId)
    {
        $chapterMaterial = new ChapterMaterialResource($this->service->getById($courseChapterId, $chapterMaterialId));

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $chapterMaterial->response()->getData(true)
        ], 200);
    }
}
