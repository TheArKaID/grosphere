<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ParentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->resource ? [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'students' => StudentResource::collection($this->whenLoaded('students')),
            'name' => $this->user->name,
            'email' => $this->user->email,
            'phone' => $this->user->phone,
            'address' => $this->address,
            'status' => $this->user->status,
            'created_at' => $this->created_at
        ] : [];
    }
}
