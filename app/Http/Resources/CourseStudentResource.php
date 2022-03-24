<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseStudentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        parent::wrap('course_students');

        return $this->resource ? [
            'id' => $this->id,
            'status' => $this->status,
            'detail' => [
                'duration' => $this->courseWork->duration,
                'name' => $this->courseWork->class->name,
                'description' => $this->courseWork->class->description,
                'thumbnail' => $this->courseWork->class->thumbnail ? asset('storage/class/thumbnail/' . $this->courseWork->class->thumbnail) : asset('storage/class/thumbnail/default.png'),
                'type' => $this->courseWork->class->type,
                'tutor' => [
                    'id' => $this->courseWork->class->tutor->id,
                    'name' => $this->courseWork->class->tutor->user->name
                ],
                'category' => [
                    'id' => $this->courseWork->courseCategory->id,
                    'name' => $this->courseWork->courseCategory->name
                ],
            ]
        ] : [];
    }
}
