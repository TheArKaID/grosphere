<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class CourseClassResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        parent::wrap('course_classes');

        return $this->resource ? [
            'id' => $this->id,
            'duration' => $this->duration,
            'published_at' => Carbon::parse($this->published_at)->toDateTimeString(),
            'class' => [
                'id' => $this->class->id,
                'name' => $this->class->name,
                'description' => $this->class->description,
                'thumbnail' => $this->class->thumbnail ? asset('storage/class/thumbnail/' . $this->class->thumbnail) : asset('storage/class/thumbnail/default.png'),
                'type' => $this->class->type,
                'tutor' => [
                    'id' => $this->class->tutor->id,
                    'name' => $this->class->tutor->user->name
                ],
            ],
        ] : [];
    }
}
