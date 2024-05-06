<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class CurriculumResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        parent::wrap('curriculum');

        return $this->resource ? [
            'id' => $this->id,
            'subject' => $this->subject,
            'grade' => $this->grade,
            'term' => $this->term,
            'chapters' => $this->chapters,
        ] : [];
    }
}
