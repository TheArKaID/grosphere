<?php

namespace App\Http\Resources;

use Carbon\Carbon;
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
        parent::wrap('announcements');

        return $this->resource ? [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'status' => (boolean)$this->status,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s')
        ] : [];
    }
}
