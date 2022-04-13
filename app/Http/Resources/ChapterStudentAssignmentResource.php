<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ChapterStudentAssignmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        parent::wrap('student_assignments');

        return $this->resource ? [
            'id' => $this->id,
            'question' => $this->courseChapterStudent->courseChapter->chapterAssignments->task,
            'answer' => $this->answer,
            'files' => $this->getFilesPath()
        ] : [];
    }
}
