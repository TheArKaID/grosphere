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
        $liveClass = $this->liveClass()->withoutGlobalScope('agency')->first();
        $class = $liveClass->class()->withoutGlobalScope('agency')->first();
        
        $startTime = Carbon::parse($liveClass->start_time);
        $endTime = Carbon::parse($liveClass->start_time)->addMinutes($liveClass->duration);
        return $this->resource ? [
            'id' => $class->id,
            'class_name' => $class->name,
            'tutor_name' => $class->tutor->user->name,
            'user_name' => $this->user->name,
            'role' => $this->user->roles[0]->name == 'tutor' ? 'Teacher' : 'Student',
            'start_time' => $startTime->toDateTimeString(),
            'duration' => $liveClass->duration,
            'end_time' => $endTime->toDateTimeString(),
            'setting' => [
                'mic_on' => $liveClass->setting->mic_on,
                'cam_on' => $liveClass->setting->cam_on,
            ],
        ] : [];
    }
}
