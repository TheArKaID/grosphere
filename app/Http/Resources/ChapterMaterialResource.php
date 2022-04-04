<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ChapterMaterialResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        parent::wrap('chapter_materials');

        return $this->resource ? [
            'id' => $this->id,
            'course_chapter_id' => $this->course_chapter_id,
            'file' => $this->getFilePath(),
            'shown_filename' => $this->shown_filename,
            'saved_filename' => $this->saved_filename,
            'ext' => $this->ext,
            'courseChapter' => $this->courseChapter ? [
                'id' => $this->courseChapter->id,
                'title' => $this->courseChapter->title,
            ] : null,
        ] : [];
    }
}
