<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        parent::wrap('subscriptions');
        return $this->resource ? [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'course_work_id' => $this->course_work_id,
            'price' => $this->price,
            'active_days' => $this->active_days,
            'active_until' => $this->active_until,
            'total_meeting' => $this->total_meeting,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'courseStudents' => CourseStudentResource::make($this->whenLoaded('courseStudents')),
            // 'invoices' => CourseStudentResource::make($this->whenLoaded('invoices')),
            'courseWork' => CourseWorkResource::make($this->whenLoaded('courseWork')),
            'student' => StudentResource::make($this->whenLoaded('student')),
        ] : [];
    }
}
