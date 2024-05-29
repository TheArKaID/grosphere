<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CourseWorkResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        parent::wrap('course_work');

        return $this->resource ? [
            'id' => $this->id,
            'subject' => $this->subject,
            'grade' => $this->grade,
            'term' => $this->term,
            'quota' => $this->quota,
            'thumbnail' => Storage::disk('s3')->url($this->thumbnail),
            'curriculum' => $this->curriculum ? [
                'id' => $this->curriculum->id,
                'subject' => $this->curriculum->subject,
                'grade' => $this->curriculum->grade,
                'term' => $this->curriculum->term,
                'created_at' => $this->curriculum->created_at
            ] : [],
            'classSessions' => $this->whenLoaded('classSessions', function () {
                return ClassSessionResource::collection($this->classSessions);
            }),
        ] : [];
    }
}
