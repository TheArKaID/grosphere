<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class LiveClassResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        parent::wrap('live_classes');

        return $this->resource ? [
            'id' => $this->id,
            'duration' => $this->duration,
            'start_time' => Carbon::parse($this->start_time)->toDateTimeString(),
            'class' => [
                'id' => $this->class->id,
                'name' => $this->class->name,
                'description' => $this->class->description,
                'thumbnail' => $this->class->thumbnail ? asset('storage/class/thumbnail/' . $this->class->thumbnail) : asset('storage/class/thumbnail/default.png'),
                'type' => $this->class->type,
                'tutor' => [
                    'id' => $this->class->tutor->id,
                    'name' => $this->class->tutor->user->name
                ],
            ],
            'setting' => [
                'mic_on' => $this->setting->mic_on,
                'cam_on' => $this->setting->cam_on,
            ],
        ] : [];
    }
}
