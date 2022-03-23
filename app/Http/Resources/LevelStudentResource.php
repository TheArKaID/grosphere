<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LevelStudentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        parent::wrap('level-students');

        return $this->resource ? [
            'id' => $this->id,
            'level_id' => $this->level_id,
            'student_id' => $this->student_id,
            'status' => $this->status,
        ] : [];
    }
}
