<?php

namespace App\Services;

use App\Exceptions\MessageException;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class MessageService
{
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
}
