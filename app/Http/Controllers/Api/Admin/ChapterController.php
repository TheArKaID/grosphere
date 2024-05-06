<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreChapterRequest;
use App\Http\Requests\UpdateChapterRequest;
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
        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $this->chapterService->getAll($curriculum->id)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreChapterRequest $request, Curriculum $curriculum)
    {
        $chapter = $this->chapterService->create($curriculum->id, $request->validated());
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
