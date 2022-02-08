<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AnnouncementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        parent::wrap('annoucement');

        return $this->resource ? [
            'id' => $this->id,
            'name' => $this->name,
            'message' => $this->message,
            'to' => $this->getToName(),
            'show_until' => $this->show_until,
        ] : [];
    }
}
