<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class AttendanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        parent::wrap('attendance');

        return $this->resource ? [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'guardian' => $this->guardian,
            'temperature' => $this->temperature,
            'remark' => $this->remark,
            'type' => $this->type,
            'proof' => $this->proof,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($this->updated_at)->format('Y-m-d H:i:s'),
            'student' => new StudentResource($this->student),
            'out' => $this->out
        ] : [];
    }
}