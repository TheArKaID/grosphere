<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class TeacherResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        parent::wrap('teacher');
        return $this->resource ? [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'name' => $this->user->name,
            'email' => $this->user->email,
            'phone' => $this->user->phone,
            'courseTeachers' => $this->whenLoaded('courseTeachers') ? CourseTeacherResource::collection($this->courseTeachers) : [],
            'status' => $this->user->status,
            'photo' => Storage::disk('s3')->url('teachers/' . $this->id . '.png'),
            'created_at' => $this->created_at
        ] : [];
    }
}
