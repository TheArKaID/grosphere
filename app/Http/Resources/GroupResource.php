<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        parent::wrap('groups');
        return $this->resource ? [
            'id' => $this->id,
            'name' => $this->name,
            'students' => $this->students ? $this->students->map(function ($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->user->name,
                    'email' => $student->user->email,
                ];
            }) : [],
            'classes' => $this->classes ? $this->classes->map(function ($class) {
                return [
                    'id' => $class->id,
                    'name' => $class->name,
                    'description' => $class->description,
                ];
            }) : [],
        ] : [];
    }
}
