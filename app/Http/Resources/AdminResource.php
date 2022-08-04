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
        parent::wrap('admins');
        return $this->resource ? [
            'id' => $this->id,
            'name' => $this->user->name,
            'agency_id' => $this->agency_id,
            'email' => $this->user->email,
            'phone' => $this->user->phone,
            'agency' => $this->user->agency ? [
                'id' => $this->user->agency->id,
                'name' => $this->user->agency->name,
                'website' => $this->user->agency->website,
            ] : [],
            'created_at' => $this->created_at
        ] : [];
    }
}
