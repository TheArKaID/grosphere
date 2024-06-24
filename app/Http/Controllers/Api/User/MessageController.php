<?php

namespace App\Http\Controllers\Api\User;

use App\Exceptions\MessageException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Resources\MessageDetailResource;
use App\Http\Resources\MessageSenderResource;
use App\Http\Resources\RecipientResource;
use App\Services\MessageService;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function __construct(
        private MessageService $service
    ) {
        $this->service = $service;
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
        $messages = MessageSenderResource::collection($this->service->getConversations());

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $messages
        ], 200);
    }

    /**
     * Store a new message
     * 
     * @param StoreMessageRequest $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreMessageRequest $request)
    {
        $data = $request->validated();

        $this->service->storeMessage($data);

        return response()->json([
            'status' => 200,
            'message' => 'Message sent successfully'
        ], 200);
    }

    /**
     * Show a message
     * 
     * @param Request $request
     * @param string $message
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, string $message)
    {
        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => MessageDetailResource::collection($this->service->getConversation($message))
        ], 200);
    }

    /**
     * Update a message
     * 
     * @param Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {

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
        $users = RecipientResource::collection($this->service->getRecipients(search: $request->get('search')));

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
