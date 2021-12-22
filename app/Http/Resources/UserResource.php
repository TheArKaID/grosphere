<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

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
            // 'role' => array_merge($this->roles->pluck('name')->toArray(), $this->roles->pluck('readable_name')->toArray()),
            'roles' => $this->roles->pluck('name'),
            'created_at' => $this->created_at
        ];
    }
}
