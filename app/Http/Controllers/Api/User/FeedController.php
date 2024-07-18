<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFeedRequest;
use App\Http\Requests\UpdateFeedRequest;
use App\Http\Resources\FeedResource;
use App\Services\FeedService;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    public function __construct(
        protected FeedService $service
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $feeds = FeedResource::collection($this->service->get());

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $feeds->response()->getData(true)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFeedRequest $request)
    {
        $feed = $this->service->create($request->validated());

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => FeedResource::make($feed->load(['images', 'user']))
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $feed = new FeedResource($this->service->find($id));

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $feed
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFeedRequest $request, string $id)
    {
        $feed = $this->service->update($id, $request->validated());

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => FeedResource::make($feed->load(['images', 'user']))
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->service->delete($id);

        return response()->json([
            'status' => 200,
            'message' => 'Feed deleted successfully',
        ], 200);
    }

    /**
     * Comment on the feed.
     */
    public function comment(Request $request, string $id)
    {
        $request->validate([
            'content' => 'required|string'
        ]);
        $this->service->comment($id, $request->content);

        return response()->json([
            'status' => 200,
            'message' => 'Commented successfully'
        ], 200);
    }
}
