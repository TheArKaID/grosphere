<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class FeedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        parent::wrap('feeds');

        return $this->resource ? [
            'id' => $this->id,
            'content' => $this->content,
            'privacy' => $this->privacy,
            'user' => new UserResource($this->whenLoaded('user')),
            'images' => FeedImageResource::collection($this->whenLoaded('images')),
            'created_at' => $this->created_at
        ] : [];
    }
}
