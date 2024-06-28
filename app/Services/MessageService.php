<?php

namespace App\Services;

use App\Events\NewMessage;
use App\Exceptions\MessageException;
use App\Jobs\StoreFileMessage;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            ->with(['sender'])
            ->get();

        // Extract unique conversations
        $conversations = $messages->map(function ($message) use ($userId) {
            return $message->sender_id == $userId ? $message->getRecipient() : $message->sender;
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

        $classGroupConversations = $this->getClassGroupConversations($userId, $conversations->pluck('id')->toArray());

        return $classGroupConversations ? $lastMessages->merge($classGroupConversations) : $lastMessages;
    }

    /**
     * Get all Class Group as Conversations
     * 
     * @param string|null $userId
     * 
     * @return mixed
     */
    public function getClassGroupConversations(string $userId = null, array $excludes = []): mixed
    {
        if (!$userId) {
            $userId = Auth::id();
        }

        $user = User::find($userId);

        if (!$user) {
            throw new MessageException('User not found');
        }

        $role = $user->roles()->first()?->name;
        if ($role == 'admin') {
            $classGroups = app()->make(ClassGroupService::class)->getAll();
        } else {
            $classGroups = $user->detail->classGroups;
        }

        // Exlcude some class groups that id in $excludes
        if (count($excludes)) {
            $classGroups = $classGroups->filter(function ($classGroup) use ($excludes) {
                return !in_array($classGroup->id, $excludes);
            });
        }
        // Retrieve last message for each class group in the Message model
        $lastMessages = $classGroups?->map(function ($classGroup) use ($userId) {
            $message = $this->model->where('recipient_group_id', $classGroup->id)
                ->orWhere('sender_id', $userId)
                ->where('recipient_id', $classGroup->teacher_id)
                ->orderBy('created_at', 'desc')
                ->first();

            $unreadCount = $this->model->where('recipient_group_id', $classGroup->id)
                ->where('recipient_id', $userId)
                ->where('is_read', false)
                ->count();

            return [
                'user' => $classGroup,
                'message' => $message,
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

        $messages = $this->model->orderBy('created_at', 'asc')
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

        return $classGroups ? $users->merge($classGroups) : $users;
    }

    /**
     * Store new messages
     * 
     * @param array $data
     * 
     * @return void
     */
    public function storeMessages(array $data): void
    {
        DB::beginTransaction();
        try {
            $data['sender_id'] = Auth::id();
            $recipients = $data['recipient_ids'];
            unset($data['recipient_ids']);
            foreach ($recipients as $recipientId) {
                $type = $this->recipientExists($recipientId);
                if (!$type) {
                    throw new MessageException('Recipient not found');
                }
                if ($type == 'user') {
                    $data['recipient_id'] = $recipientId;
                    $data['recipient_group_id'] = null;
                } else {
                    $data['recipient_group_id'] = $recipientId;
                    $data['recipient_id'] = null;
                }
                $m = $this->model->create($data);

                NewMessage::dispatch($m, $type);

                if (isset($data['attachments']) && count($data['attachments'])) {
                    foreach ($data['attachments'] as $attachment) {
                        StoreFileMessage::dispatch($m, $attachment)->afterCommit();
                    }
                }
            }

            DB::commit();
        } catch (\Throwable $th) {
            Log::error($th);
            DB::rollBack();

            throw new MessageException($th->getMessage());
        }
    }

    /**
     * Check if recipient exists
     * 
     * @param string $recipientId
     * 
     * @return bool|string
     */
    public function recipientExists(string $recipientId): bool|string
    {
        // Recipient could be user or class group
        return User::find($recipientId) ? 'user' : (app()->make(ClassGroupService::class)->getOne($recipientId, false) ? 'class_group' : false);
    }
}
