<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class StudentClassResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        parent::wrap('student_classes');

        $student = $this->courseStudent?->student ?? $this->student;
        return $this->resource ? [
            'student_name' => $student?->user->first_name . ' ' . $student?->user->last_name,
            'avatar' => Storage::disk('s3')->url('students/' . $student->id . '.png'),
        ] : [];
    }
}
