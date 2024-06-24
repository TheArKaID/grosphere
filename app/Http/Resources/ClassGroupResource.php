<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClassGroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        parent::wrap('class_groups');
        return $this->resource ? [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'teacher' => $this->whenLoaded('teacher', function () {
                return new TeacherResource($this->teacher);
            }),
            'students' => $this->whenLoaded('students', function () {
                return StudentResource::collection($this->students);
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ] : [];
    }
}