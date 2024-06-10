<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class AgencyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        parent::wrap('agencies');

        return $this->resource ? [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'website' => $this->website,
            'about' => $this->about,
            'sub_title' => $this->sub_title,
            'color' => $this->color,
            'logo' => Storage::disk('s3')->url('agencies/' . $this->id . '.png'),
            'logo-sm' => Storage::disk('s3')->url('agencies/' . $this->id . '-sm.png'),
            'status' => $this->status,
            'admins' => $this->whenLoaded('admins', function () {
                return AdminResource::collection($this->admins);
            }),
            'active_until' => $this->active_until,
            'created_at' => $this->created_at,
        ] : [];
    }
}
