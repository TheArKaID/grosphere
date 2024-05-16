<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class GuardianResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        parent::wrap('guardians');
        return $this->resource ? [
            'id' => $this->id,
            'students' => StudentResource::collection($this->whenLoaded('students')),
            'name' => $this->user?->name,
            'email' => $this->user?->email,
            'phone' => $this->user?->phone,
            'address' => $this->address,
            'status' => $this->user?->status,
            'photo' => Storage::disk('s3')->url('guardians/' . $this->id . '.png'),
            'created_at' => $this->created_at
        ] : [];
    }
}
