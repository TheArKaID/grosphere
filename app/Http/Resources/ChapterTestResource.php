<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class ChapterTestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        parent::wrap('course_category');

        return $this->resource ? [
            'id' => $this->id,
            'course_chapter_id' => $this->course_chapter_id,
            'title' => $this->title,
            'duration' => $this->duration,
            'attempt' => $this->attempt,
            'available_at' => Carbon::parse($this->available_at)->toDateTimeString(),
            'available_until' => Carbon::parse($this->available_until)->toDateTimeString(),
        ] : [];
    }
}
