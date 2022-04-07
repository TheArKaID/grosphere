<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ChapterAssignmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        parent::wrap('chapter_assignments');

        return $this->resource ? [
            'id' => $this->id,
            'course_chapter_id' => $this->course_chapter_id,
            'task' => $this->task,
            'courseChapter' => $this->courseChapter ? [
                'id' => $this->courseChapter->id,
                'title' => $this->courseChapter->title,
            ] : null,
        ] : [];
    }
}
