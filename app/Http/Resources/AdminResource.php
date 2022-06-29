<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminResource extends JsonResource
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
            'name' => $this->user->name,
            'institute_id' => $this->institute_id,
            'email' => $this->user->email,
            'phone' => $this->user->phone,
            'institute' => $this->user->institute ? [
                'id' => $this->user->institute->id,
                'name' => $this->user->institute->name,
                'website' => $this->user->institute->website,
            ] : [],
            'created_at' => $this->created_at
        ] : [];
    }
}
