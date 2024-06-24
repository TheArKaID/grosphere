<?php

namespace App\Services;

use App\Exceptions\MessageException;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MessageService
{
    public function __construct(
        private Message $model 
    ) {}

    /**
     * Get all logged in user messages
     * 
     * @param string|null $userId
     * 
     * @return Collection
     */
    public function getMessages(string $userId = null): Collection
    {
        if (!$userId) {
            $userId = Auth::id();
        }

        $user = User::find($userId);

        if (!$user) {
            throw new MessageException('User not found');
        }

        return $this->model->where('sender_id', $userId)->orWhere('recipient_id', $userId)->get();
    }

    /**
     * Get User that already sent messages to me or I sent messages to
     * 
     * @param string|null $userId
     * 
     * @return mixed
     */
    public function getConversations(string $userId = null): mixed
    {
        if (!$userId) {
            $userId = Auth::id();
        }

        $user = User::find($userId);

        if (!$user) {
            throw new MessageException('User not found');
        }

        // Retrieve all messages involving the user and eager load the related sender and recipient
        $messages = $this->model->where('sender_id', $userId)
            ->orWhere('recipient_id', $userId)
            ->with(['sender', 'recipient'])
            ->get();

        // Extract unique conversations
        $conversations = $messages->map(function ($message) use ($userId) {
            return $message->sender_id == $userId ? $message->recipient : $message->sender;
        })->unique('id');

        // Get the last message and unread count for each conversation
        $lastMessages = $conversations->map(function ($conversation) use ($messages, $userId) {
            $conversationMessages = $messages->filter(function ($message) use ($conversation, $userId) {
                // Recipient could be null if the user has been deleted
                return ($message->sender_id == $conversation?->id && $message->recipient_id == $userId) ||
                    ($message->sender_id == $userId && $message->recipient_id == $conversation?->id);
            });

            $latestMessage = $conversationMessages->sortByDesc('created_at')->first();
            $unreadCount = $conversationMessages->where('is_read', false)->count();

            return [
                'user' => $conversation,
                'message' => $latestMessage,
                'unread' => $unreadCount
            ];
        });

        return $lastMessages;
    }

    /**
     * Get detail Conversation between logged in user and another user
     * 
     * @param string $userId
     * 
     * @return mixed
     */
    public function getConversation(string $userId): mixed
    {
        $user = User::find($userId);

        if (!$user) {
            throw new MessageException('User not found');
        }

        $messages = $this->model->orderBy('created_at', 'desc')
        ->where(function ($query) use ($userId) {
            $query->where('sender_id', Auth::id())->where('recipient_id', $userId);
        })->orWhere(function ($query) use ($userId) {
            $query->where('sender_id', $userId)->where('recipient_id', Auth::id());
        })->get();

        return $messages;
    }
    /**
     * Get Users are allowed to send messages to
     * 
     * @param string|null $userId
     * @param string|null $search
     * 
     * @return Collection
     */
    public function getRecipients(string $userId = null, string|null $search = null): Collection
    {
        if (!$userId) {
            $userId = Auth::id();
        }

        $user = User::find($userId);

        if (!$user) {
            throw new MessageException('User not found');
        }

        $users = $search ? User::where(function ($query) use ($search) {
            $query->where('first_name', 'like', "%$search%")->orWhere('last_name', 'like', "%$search%");
        }) : User::query();

        $role = $user->roles()->first()?->name;
        switch ($role) {
            case 'superadmin':
                $userRecipients = ['admin'];
                break;
            case 'admin':
                $userRecipients = ['superadmin', 'guardian', 'student', 'teacher'];
                break;
            case 'guardian':
                $userRecipients = ['admin', 'teacher'];
                break;
            case 'student':
                $userRecipients = ['teacher'];
                break;
            case 'teacher':
                $userRecipients = ['admin', 'student', 'parent'];
                break;
            default:
                throw new MessageException('User not found');
        }

        $users = $users->whereHas('roles', function ($query) use ($userRecipients) {
            $query->whereIn('name', $userRecipients);
        })->get();

        $classGroups = (function (User $user, string $role) {
            switch ($role) {
                case 'admin':
                    return app()->make(ClassGroupService::class)->getAll();
                    break;
                
                default:
                    return $user->detail->classGroups;
                    break;
            }
        })($user, $role);

        return $users->merge($classGroups);
    }

    /**
     * Store a new message
     * 
     * @param array $data
     * 
     * @return void
     */
    public function storeMessage(array $data): void
    {
        DB::beginTransaction();
        $mId = null;
        try {
            $data['sender_id'] = Auth::id();
            $m = $this->model->create($data);
            $mId = $m->id;

            if (isset($data['attachments']) && count($data['attachments'])) {
                foreach ($data['attachments'] as $attachment) {
                    Storage::disk('s3')->put('messages' . DIRECTORY_SEPARATOR . $m->id, $attachment);
                }
            }
            DB::commit();
        } catch (\Throwable $th) {
            Log::error($th);
            DB::rollBack();
            if ($mId) {
                Storage::disk('s3')->deleteDirectory('messages' . DIRECTORY_SEPARATOR . $mId);
            }
            throw new MessageException('Failed to send message');
        }
    }
}
