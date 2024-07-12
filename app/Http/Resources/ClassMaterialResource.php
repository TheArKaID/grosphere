<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ClassMaterialResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        parent::wrap('class_materials');

        return $this->resource ? [
            'id' => $this->id,
            'class_session_id' => $this->class_session_id,
            'teacher_file_id' => $this->teacher_file_id,
            'class_session' => $this->whenLoaded('classSession', function () {
                return $this->classSession->only(['id', 'name']);
            }),
            'name' => $this->detail->name,
            'content' => $this->content,
            'content_type' => $this->content_type,
            'file_name' => $this->detail->file_name,
            'file_size' => $this->detail->file_size,
        ] : [];
    }
}
