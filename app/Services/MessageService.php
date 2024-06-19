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
    public function getRecipients(string $userId = null): Collection
    {
        if (!$userId) {
            $userId = Auth::id();
        }

        $user = User::find($userId);

        switch ($user->roles()->first()?->name) {
            case 'superadmin':
                return User::whereHas('roles', function ($query) {
                    $query->whereIn('name', ['admin']);
                })->get();
                break;
            case 'admin':
                return User::whereHas('roles', function ($query) {
                    $query->whereIn('name', ['superadmin', 'guardian', 'student', 'teacher']);
                })->get();
                break;
            case 'guardian':
                return User::whereHas('roles', function ($query) {
                    $query->whereIn('name', ['admin', 'teacher']);
                })->get();
                break;
            case 'student':
                return User::whereHas('roles', function ($query) {
                    $query->whereIn('name', ['teacher']);
                })->get();
                break;
            case 'teacher':
                return User::whereHas('roles', function ($query) {
                    $query->whereIn('name', ['admin', 'student', 'parent']);
                })->get();
                break;
            default:
                throw new MessageException('User not found');
        }
    }
}
