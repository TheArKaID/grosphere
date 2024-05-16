<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class StudentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        parent::wrap('students');
        return $this->resource ? [
            'id' => $this->id,
            'id_number' => $this->id_number,
            'name' => $this->user->name,
            'email' => $this->user->email,
            'phone' => $this->user->phone,
            'gender' => $this->gender,
            'birth_date' => Carbon::parse($this->birth_date)->toDateTimeString(),
            'birth_place' => $this->birth_place,
            'address' => $this->address,
            'status' => $this->user->status,
            'photo' => Storage::disk('s3')->url('students/' . $this->id . '.png'),
            'guardians' => GuardianResource::collection($this->whenLoaded('guardians')),
            'courseStudents' => CourseStudentResource::collection($this->whenLoaded('courseStudents')),
            // 'studentClasses' => CourseStudentResource::collection($this->whenLoaded('studentClasses')),
            'subscriptions' => SubscriptionResource::collection($this->whenLoaded('subscriptions')),
            'created_at' => $this->created_at
        ] : [];
    }
}
