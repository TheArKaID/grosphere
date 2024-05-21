<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        parent::wrap('users');
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'roles' => $this->roles->pluck('name'),
            'photo' => $this->getPhoto(),
            'detail' => $this->getDetail(),
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
                return asset('storage/user/' . $this->id . "/profile.png");
                break;
            default:
                return asset('images/user/profile.png');
                break;
        }
    }
}
