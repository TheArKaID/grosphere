<?php

namespace App\Http\Controllers\Api\Admin;

use App\Exceptions\ModelGetEmptyException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTutorRequest;
use App\Http\Requests\UpdateTutorRequest;
use App\Http\Resources\TutorResource;
use App\Models\Tutor;
use App\Services\TutorService;

class TutorController extends Controller
{
    private $tutorService;

    public function __construct(TutorService $tutorService)
    {
        $this->tutorService = $tutorService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tutors = TutorResource::collection($this->tutorService->getAll());

        if ($tutors->count() == 0) {
            throw new ModelGetEmptyException("Tutor");
        }

        return response()->json([
            'status' => 200,
            'message' => "Success",
            'response' => $tutors->response()->getData(true)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreTutorRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTutorRequest $request)
    {
        $validated = $request->validated();
        $tutor = $this->tutorService->create($validated);

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'response' => new TutorResource($tutor)
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $tutor
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $tutor = $this->tutorService->getById($id);

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'response' => new TutorResource($tutor)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateTutorRequest  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTutorRequest $request, int $id)
    {
        $validated = $request->validated();
        $tutor = $this->tutorService->update($id, $validated);

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'response' => new TutorResource($tutor)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $this->tutorService->delete($id);

        return response()->json([
            'status' => 200,
            'message' => 'Tutor deleted successfully',
        ], 200);
    }
}
