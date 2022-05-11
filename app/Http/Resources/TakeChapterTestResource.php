<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class TakeChapterTestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        parent::wrap('course_tests');

        return $this->resource ? [
            'title' => $this->courseChapterStudent->courseChapter->chapterTest->title,
            'duration' => $this->courseChapterStudent->courseChapter->chapterTest->duration,
            'attempt' => $this->courseChapterStudent->courseChapter->chapterTest->attempt,
            'available_until' => Carbon::parse($this->courseChapterStudent->courseChapter->chapterTest->available_until)->toDateTimeString(),
        ] : [];
    }
}
