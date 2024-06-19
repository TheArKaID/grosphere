<?php

namespace App\Http\Controllers\Api\User;

use App\Exceptions\MessageException;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Services\MessageService;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function __construct(
        private MessageService $msgService
    ) {
        $this->msgService = $msgService;
    }

    /**
     * Get user profile
     * 
     * @param Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => ''
        ], 200);
    }

    /**
     * Store a new message
     * 
     * @param Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'recipient_id' => 'required|uuid|exists:users,id',
            'message' => 'required|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,mp4|max:20480'
        ]);

        $this->msgService->storeMessage($data);

        return response()->json([
            'status' => 200,
            'message' => 'Message sent successfully',
            'data' => ''
        ], 200);
    }

    /**
     * Get Users are allowed to send messages to
     * 
     * @param Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRecipients(Request $request)
    {
        $users = UserResource::collection($this->msgService->getRecipients(search: $request->get('search')));

        if ($users->count() == 0) {
            throw new MessageException('No available recipients');
        }

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $users
        ], 200);
    }
}
