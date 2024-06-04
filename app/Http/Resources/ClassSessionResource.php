<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ClassSessionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        parent::wrap('class_sessions');

        return $this->resource ? [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'date' => $this->date,
            'time' => $this->time,
            'quota' => $this->quota,
            'type' => $this->type,
            'thumbnail' => $this->thumbnail ? Storage::disk('s3')->url($this->thumbnail) : '',
            'used_quota' => count($this->studentClasses),
            'teacher' => $this->whenLoaded('teacher', function () {
                return new TeacherResource($this->teacher);
            }),
            'course_work' => $this->whenLoaded('courseWork', function () {
                return new CourseWorkResource($this->courseWork);
            }),
            'class_materials' => $this->whenLoaded('classMaterials', function () {
                return ClassMaterialResource::collection($this->classMaterials);
            }),
            'students' => StudentClassResource::collection($this->enrolled()),
            'created_at' => $this->created_at
        ] : [];
    }
}
