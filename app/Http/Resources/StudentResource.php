<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

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
            'user_id' => $this->user_id,
            'name' => $this->user->name,
            'email' => $this->user->email,
            'phone' => $this->user->phone,
            'gender' => $this->gender,
            'birth_date' => Carbon::parse($this->birth_date)->toDateTimeString(),
            'birth_place' => $this->birth_place,
            'address' => $this->address,
            'status' => $this->user->status,
            'guardians' => $this->guardians ?? [],
            'created_at' => $this->created_at
        ] : [];
    }
}
