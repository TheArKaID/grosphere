<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class RecipientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        parent::wrap('recipients');

        return $this->first_name ? [
            'id' => $this->id,
            'name' => $this->first_name . ' ' . $this->last_name,
            'type' => 'user',
            'role' => $this->roles->pluck('name')[0],
            'photo' => $this->getPhoto()
        ] : [
            'id' => $this->id,
            'name' => $this->name,
            'type' => 'class-group',
            
        ];
    }

    function getPhoto() {
        switch ($this->roles[0]->name) {
            case 'student':
                return Storage::disk('s3')->url('students/' . $this->detail->id . '.png');
                break;
            case 'teacher':
                return Storage::disk('s3')->url('teachers/' . $this->detail->id . '.png');
                break;
            case 'guardian':
                return Storage::disk('s3')->url('guardians/' . $this->detail->id . '.png');
                break;
            case 'admin':
                return Storage::disk('s3')->url('admins/' . $this->detail->id . '.png');
                break;
            default:
                return asset('images/user/profile.png');
                break;
        }
    }
}
