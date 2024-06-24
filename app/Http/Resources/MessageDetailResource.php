<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class MessageDetailResource extends JsonResource
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
        return $this->resource ? [
            'id' => $this['id'],
            'message' => $this['message'],
            'is_me' => $this['sender_id'] === auth()->id(),
            'is_read' => $this['is_read'],
            'created_at' => $this['created_at'],
            'attachments' => $this->getAttachments($this['id']),
        ] : [];
    }

    function getAttachments(string $messageId) {
        if ($files = Storage::disk('s3')->files('messages/' . $messageId)) {
            return array_map(function ($file) {
                // Return the data type and url file 
                return [
                    'type' => Storage::disk('s3')->mimeType($file),
                    'url' => Storage::disk('s3')->url($file)
                ];
            }, $files);
        } else {
            return [];
        }
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
