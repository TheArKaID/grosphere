<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class LeaveRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        parent::wrap('leave-requests');

        return $this->resource ? [
            'id' => $this->id,
            'from_date' => $this->from_date,
            'to_date' => $this->to_date,
            'reason' => $this->reason,
            'status' => $this->status,
            'tag' => $this->whenLoaded('tag', function () {
                return $this->tag->name;
            }),
            'student' => $this->whenLoaded('student', function () {
                return [
                    'id' => $this->student->id,
                    'first_name' => $this->student->user->first_name,
                    'last_name' => $this->student->user->last_name,
                ];
            }),
            'guardian' => $this->whenLoaded('guardian', function () {
                return [
                    'id' => $this->guardian->id,
                    'first_name' => $this->guardian->user->first_name,
                    'last_name' => $this->guardian->user->last_name,
                ];
            }),
            'photo' => Storage::disk('s3')->exists('leave-requests/' . $this->id . '.png') ? Storage::disk('s3')->url('leave-requests/' . $this->id . '.png') : null
        ] : [];
    }
}
