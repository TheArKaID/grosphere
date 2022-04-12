<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseWorkResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        parent::wrap('course_works');

        return $this->resource ? [
            'id' => $this->id,
            'name' => $this->class->name,
            'description' => $this->class->description,
            'thumbnail' => $this->class->thumbnail ? asset('storage/class/thumbnail/' . $this->class->thumbnail) : asset('storage/class/thumbnail/default.png'),
            'type' => $this->class->type,
            'total_chapter' => $this->courseChapters->count(),
            'tutor' => [
                'id' => $this->class->tutor->id,
                'name' => $this->class->tutor->user->name
            ],
            'category' => [
                'id' => $this->courseCategory->id,
                'name' => $this->courseCategory->name
            ],
        ] : [];
    }
}
