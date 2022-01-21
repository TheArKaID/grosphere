<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LiveClassResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        parent::wrap('live_classes');
        return $this->resource ? [
            'id' => $this->id,
            'duration' => $this->duration,
            'start_time' => $this->start_time,
            'class' => [
                'id' => $this->class->id,
                'name' => $this->class->name,
                'description' => $this->class->description,
                'thumbnail' => $this->class->thumbnail ? asset('class/thumbnail/' . $this->class->thumbnail) : asset('class/thumbnail/default.png'),
                'type' => $this->class->type,
                'tutor' => [
                    'id' => $this->class->tutor->id,
                    'name' => $this->class->tutor->user->name
                ],
            ],
        ] : [];
    }
}