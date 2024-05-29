<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

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
            'status' => $this->status,
            'created_at' => $this->created_at,
        ] : [];
    }
}
