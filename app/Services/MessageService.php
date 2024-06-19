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
     * Get Users are allowed to send messages to
     * 
     * @param string|null $userId
     * 
     * @return Collection
     */
    public function getRecipients(string $userId = null, string $search = ''): Collection
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

        switch ($user->roles()->first()?->name) {
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

        return  $users->whereHas('roles', function ($query) use ($userRecipients) {
            $query->whereIn('name', $userRecipients);
        })->get();
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
