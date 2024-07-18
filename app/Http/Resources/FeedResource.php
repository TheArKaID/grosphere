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
            'likes' => [
                'count' => $this->likes->count(),
                'data' => UserResource::collection($this->whenLoaded('likes'))
            ],
            'user' => new UserResource($this->whenLoaded('user')),
            'images' => FeedImageResource::collection($this->whenLoaded('images')),
            'comments' => FeedCommentResource::collection($this->whenLoaded('comments')),
            'created_at' => $this->created_at
        ] : [];
    }
}
