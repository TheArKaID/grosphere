<?php

namespace App\Http\Controllers\Api\Admin;

use App\Exceptions\ModelGetEmptyException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreChapterRequest;
use App\Http\Requests\UpdateChapterRequest;
use App\Http\Resources\ChapterResource;
use App\Models\Chapter;
use App\Models\Curriculum;
use App\Services\ChapterService;
use Illuminate\Http\Request;

class ChapterController extends Controller
{
    protected $chapterService;

    public function __construct(ChapterService $chapterService)
    {
        $this->chapterService = $chapterService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Curriculum $curriculum)
    {
        $chapters = ChapterResource::collection($this->chapterService->getAll($curriculum->id));

        if ($chapters->count() == 0) {
            // throw new ModelGetEmptyException("Chapter");
        }

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $chapters->response()->getData(true)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreChapterRequest $request, Curriculum $curriculum)
    {
        $data = $request->validated();
        $chapter = new ChapterResource($this->chapterService->create($curriculum->id, $data));

        return response()->json([
            'status' => 201,
            'message' => 'Chapter created',
            'data' => $chapter
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Curriculum $curriculum, Chapter $chapter)
    {
        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $chapter
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateChapterRequest $request, Curriculum $curriculum, Chapter $chapter)
    {
        $this->chapterService->update($chapter, $request->validated());
        return response()->json([
            'status' => 200,
            'message' => 'Chapter updated',
            'data' => $chapter
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Curriculum $curriculum, Chapter $chapter)
    {
        $this->chapterService->delete($chapter);
        return response()->json([
            'status' => 200,
            'message' => 'Chapter deleted',
            'data' => $chapter
        ], 200);
    }
}
