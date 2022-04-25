<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseChapterStudentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        parent::wrap('chapters');

        $resource = $this->resource ? [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'order' => $this->order,
            'status' => count($this->courseChapterStudents) > 0 ? $this->courseChapterStudents[0]->status : false,
        ] : [];
        $resource['content'] = $this->content ?? null;

        return $resource;
    }
}
