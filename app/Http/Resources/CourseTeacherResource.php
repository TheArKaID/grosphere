<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseTeacherResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        parent::wrap('course_teacher');

        return $this->resource ? [
            'id' => $this->id,
            'status' => $this->status,
            'detail' => $this->whenLoaded('teacher') ? [
                'id' => $this->teacher->id,
                'name' => $this->teacher->user->name,
                'email' => $this->teacher->user->email,
                'phone' => $this->teacher->user->phone,
                'status' => $this->teacher->user->status,
                'created_at' => $this->teacher->created_at
            ] : [],
        ] : [];
    }
}
