<?php

namespace App\Services;

use App\Events\NewMessage;
use App\Exceptions\MessageException;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
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

        $classGroupConversations = $this->getClassGroupConversations($userId, $conversations->pluck('id')->toArray());

        $lastMessage = $classGroupConversations ? $lastMessages->merge($classGroupConversations) : $lastMessages;

        return $lastMessage->sortByDesc('message.created_at');
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
     * Get detail Conversation between logged in user and another user
     * 
     * @param string $id
     * 
     * @return mixed
     */
    public function getConversation(string $id): mixed
    {
        $user = User::find($id);

        if (!$user) {
            app()->make(ClassGroupService::class)->getOne($id, true);
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
        });

        $messages = $messages->get();

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
                    $data['is_read'] = true;
                }
                $m = $this->model->create($data);

                NewMessage::dispatch($m, $type);

                if ($type == 'user' && $m->recipientUser->email) {
                    $template = view('emails.new-message', [
                        'sender' => $m->sender->first_name . ' ' . $m->sender->last_name,
                        'receiver' => $m->recipientUser->first_name . ' ' . $m->recipientUser->last_name,
                        'message' => $m->message,
                        'messageTime' => Carbon::parse($m->created_at)->format('Y-m-d H:i:s')
                    ])->render();

                    (new MailService())->sendMailWithAttachment([
                        [
                            'email' => $m->recipientUser->email,
                            'name' => $m->recipientUser->first_name . ' ' . $m->recipientUser->last_name
                        ]
                    ], 'New Message', $template, 
                    [
                        [
                            'content' => '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',
                            'filename' => 'logo-up.png',
                            'type' => 'image/png',
                            'disposition' => 'inline'
                        ],
                        [
                            'content' => 'iVBORw0KGgoAAAANSUhEUgAAABsAAAAYCAYAAAALQIb7AAAACXBIWXMAAAsTAAALEwEAmpwYAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAEeSURBVHgBtZaLrcIwDEVvmaAjdARGyAhvg9cRGKEjsAFsABu0G8AGgQnKBsYRqaiifNzIHMlKqjq5tvNpgQhE1LGNbDN9cP0e2nihRSTkAE18FilcEC00cBNRmersdsGzJOp/aEHp9VpjoAFPNAjERlTQRMRcKS3iJX2yPXz/znZ1bdM0L9QSZOfK+if0s4Hd2E5sXU5s2ZU26whx2S3ljoyPqoOsEqNAcMhNcIIQ9jUCsXmXGNxjA7xBJm6mglsbE+qWBcYGBNndwgFtUP9N9yD7XzJifSyjNQYb8MEew7Wi9dHxJbCRaI6owAfu5tzHXqSYoQmVz4iBEm7rm4LPHopi9Zdohdi94HOFFoktvzBAGy94pu9XeqIf/Lq9AWnM0iwmnClHAAAAAElFTkSuQmCC',
                            'filename' => 'logo-down.png',
                            'type' => 'image/png',
                            'disposition' => 'inline'
                        ]
                    ]);
                }

                if (isset($data['attachments']) && count($data['attachments'])) {
                    foreach ($data['attachments'] as $attachment) {
                        $path = 'messages' . DIRECTORY_SEPARATOR . $m->id;
                        Storage::disk('s3')->put($path, $attachment);
                    }
                    $files = Storage::disk('s3')->files($path);
                    
                    $fileMessages = array_map(function ($file) {
                        return [
                            'url' => Storage::disk('s3')->url($file),
                            'type' => Storage::disk('s3')->mimeType($file)
                        ];
                    }, $files);

                    $m->attachments()->createMany($fileMessages);
                }
            }

            DB::commit();
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
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
