<?php

namespace App\Services;

use App\Events\NewMessage;
use App\Exceptions\MessageException;
use App\Jobs\MailNewMessage;
use App\Models\Message;
use App\Models\RecipientGroup;
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
        $messages = $this->model->where(function ($query) use ($userId) {
            $query->where('sender_id', $userId)
            ->orWhere('recipient_id', $userId);
        })->where('recipient_group_id', null)
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
            $unreadCount = $conversationMessages->where('is_read', false)->where('recipient_id', $userId)->count();

            return [
                'user' => $conversation,
                'message' => $latestMessage,
                'unread' => $unreadCount
            ];
        });

        if ($classGroupConversations = $this->getClassGroupConversations($userId)) {
            $lastMessages = $lastMessages->merge($classGroupConversations);
        }
        
        if ($recipientGroupConversations = $this->getRecipientGroupConversations($userId)) {
            // dd($recipientGroupConversations);
            $lastMessages = $lastMessages->merge($recipientGroupConversations);
        }

        return $lastMessages->sortByDesc('message.created_at');
    }

    /**
     * Get all Class Group as Conversations
     * 
     * @param string|null $userId
     * 
     * @return mixed
     */
    public function getClassGroupConversations(string $userId = null): mixed
    {
        if (!$userId) {
            $userId = Auth::id();
        }

        $user = User::find($userId);

        if (!$user) {
            throw new MessageException('User not found');
        }

        $role = $user->roles()->first()?->name;
        switch ($role) {
            case 'admin':
                $classGroups = app()->make(ClassGroupService::class)->getAll();
                break;
            case 'guardian':
                $classGroups = app()->make(ClassGroupService::class)->getByGuardian($user->detail->id);
                break;
            default:
                $classGroups = $user->detail->classGroups;
                break;
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
     * Get Recipient Groups as Conversations
     * 
     * @param string|null $userId
     * 
     * @return mixed
     */
    public function getRecipientGroupConversations(string $userId = null): mixed
    {
        if (!$userId) {
            $userId = Auth::id();
        }

        $user = User::find($userId);

        if (!$user) {
            throw new MessageException('User not found');
        }

        $recipientGroups = RecipientGroup::where('user_id', $userId)->get();

        $lastMessages = $recipientGroups->map(function ($recipientGroup) use ($userId) {
            $message = $this->model->where('broadcast_id', $recipientGroup->id)
                ->where('sender_id', $userId)
                ->orderBy('created_at', 'desc')
                ->first();

            // $unreadCount = $this->model->where('broadcast_id', $recipientGroup->id)
            //     ->where('sender_id', $userId)
            //     ->where('is_read', 0)
            //     ->count();

            return [
                'user' => $recipientGroup,
                'message' => $message,
                'unread' => 0
            ];
        });

        return $lastMessages;
    }
    /**
     * Get detail Conversation between logged in user and another user
     * 
     * @param string $id
     * 
     * @return mixed
     */
    public function getConversation(string $id): mixed
    {
        if (!User::find($id)) {
            if (!app()->make(ClassGroupService::class)->getOne($id, false)) {
                RecipientGroup::findOrFail($id);
            }
        }

        // Update the messages to read if logged in user not the sender
        $this->model->where('sender_id', '!=', Auth::id())->update(['is_read' => true]);

        $messages = $this->model->orderBy('created_at', 'asc')
        ->where(function ($query) use ($id) {
            $query->where('sender_id', Auth::id())->where('recipient_id', $id);
        })->orWhere(function ($query) use ($id) {
            $query->where('sender_id', $id)->where('recipient_id', Auth::id());
        })->orWhere(function ($query) use ($id) {
            $query->where('recipient_group_id', $id);
        })->orWhere(function ($query) use ($id) {
            $query->where('sender_id', Auth::id())->where('broadcast_id', $id);
        });

        $messages = $messages->get();

        // remove message with the same timestamp
        $messages = $messages->unique('created_at');

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
                $userRecipients = ['admin', 'student', 'parent', 'guardian'];
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
                case 'guardian':
                    return app()->make(ClassGroupService::class)->getByGuardian($user->detail->id);
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
            $data['message'] = $data['message'] ?? '';
            $recipients = $data['recipient_ids'];
            $groupName = $data['group_name'] ?? now()->format('Y-m-d H:i:s');
            unset($data['recipient_ids']);
            unset($data['group_name']);

            $groupRecipients = $this->sendToRecipientGroup($recipients[0]);

            if ($groupRecipients) {
                $data['broadcast_id'] = $recipients[0];
                $recipients = $groupRecipients->pluck('id')->toArray();
            } else {
                $type = $this->recipientExists($recipients[0]);
                $groupRecipients = $this->createRecipientGroup($recipients, $type, $groupName);
                $data['broadcast_id'] = $groupRecipients?->id;
            }

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
                    $data['is_read'] = true;
                }
                $m = $this->model->create($data);

                NewMessage::dispatch($m, $type);

                if ($type == 'user' && $m->recipientUser->email) {
                    MailNewMessage::dispatch($m)->afterCommit()->delay(now()->addMinute());
                }

                if (isset($data['attachments']) && count($data['attachments'])) {
                    $this->storeAttactments($m, $data['attachments']);
                }
            }

            DB::commit();
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            DB::rollBack();

            throw new MessageException($th->getMessage());
        }
    }

    function createRecipientGroup(array $recipients, string $recipientType, string $groupName) : null|RecipientGroup {
        if (count($recipients) > 1 && Auth::user()->roles()->first()->name == 'admin') {
            $group = RecipientGroup::create([
                'name' => $groupName,
                'user_id' => Auth::id()
            ]);

            if ($recipientType == 'user') {
                $group->recipientUsers()->attach($recipients);
            } elseif ($recipientType == 'class_group') {
                $group->recipientGroups()->attach($recipients);
            }
            return $group;
        }
        return null;
    }

    function storeAttactments(Message $message, array $attachments) : void {
        foreach ($attachments as $attachment) {
            $path = 'messages' . DIRECTORY_SEPARATOR . $message->id;
            Storage::disk('s3')->put($path, $attachment);
        }
        $files = Storage::disk('s3')->files($path);
        
        $fileMessages = array_map(function ($file) {
            return [
                'url' => Storage::disk('s3')->url($file),
                'type' => Storage::disk('s3')->mimeType($file)
            ];
        }, $files);

        $message->attachments()->createMany($fileMessages);
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
        return User::find($recipientId) 
            ? 'user' 
            : (app()->make(ClassGroupService::class)->getOne($recipientId, false) 
                ? 'class_group' 
                : false);
    }

    function sendToRecipientGroup(string $id) : null|Collection {
        $group = RecipientGroup::find($id);

        return $group?->recipientUsers ?? $group?->recipientGroups;
    }
}
