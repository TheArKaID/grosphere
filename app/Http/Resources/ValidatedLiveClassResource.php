<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class ValidatedLiveClassResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $liveClass = $this->liveClass;
        
        $startTime = Carbon::parse($liveClass->start_time);
        $endTime = Carbon::parse($liveClass->start_time)->addMinutes($liveClass->duration);
        return $this->resource ? [
            'class_name' => $liveClass->class->name,
            'tutor_name' => $liveClass->class->tutor->user->name,
            'user_name' => $this->user->name,
            'role' => $this->user->roles[0]->name == 'tutor' ? 'Teacher' : 'Student',
            'start_time' => $startTime->toDateTimeString(),
            'duration' => $liveClass->duration,
            'end_time' => $endTime->toDateTimeString(),
        ] : [];
    }
}
