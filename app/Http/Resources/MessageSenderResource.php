<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class MessageSenderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        parent::wrap('message_senders');
        $user = $this['user'] ?? null;
        return [
            'id' => $user?->id ?? '-',
            'name' => $user?->first_name ? $user?->first_name . ' ' . $user?->last_name : $user?->name,
            'role' => $user?->first_name ? $user?->roles()->first()->name : ($user?->user_id ? 'broadcast' : 'class-group'),
            'photo' => $user?->first_name ? $this->getPhoto() : '-',
            'message' => $this['message'] ? $this['message']->message : 'No message yet',
            'message_date' => $this['message']?->created_at?->format('Y-m-d H:i:s') ?? $user?->created_at->format('Y-m-d H:i:s'),
            'unread' => $this['unread']
        ];
    }

    function getPhoto() {
        switch ($this['user']->roles[0]->name) {
            case 'student':
                return Storage::disk('s3')->url('students/' . $this['user']->detail->id . '.png');
                break;
            case 'teacher':
                return Storage::disk('s3')->url('teachers/' . $this['user']->detail->id . '.png');
                break;
            case 'guardian':
                return Storage::disk('s3')->url('guardians/' . $this['user']->detail->id . '.png');
                break;
            case 'admin':
                return Storage::disk('s3')->url('admins/' . $this['user']->detail->id . '.png');
                break;
            default:
                return asset('images/user/profile.png');
                break;
        }
    }
}
