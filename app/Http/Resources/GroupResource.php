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
        $array = [];
        if ($this->classes) {
            $array['courseWorks'] = $this->classes->filter(function ($class) {
                return $class->courseWork;
            })->map(function ($class) {
                return [
                    'id' => $class->courseWork->id,
                    'name' => $class->name,
                    'description' => $class->description,
                    'thumbnail' => $class->thumbnail ? asset('storage/class/thumbnail/' . $class->thumbnail) : asset('storage/class/thumbnail/default.png'),
                    'tutor' => $class->tutor->user->name
                ];
            })->toArray();
            $array['liveClasses'] = $this->classes->filter(function ($class) {
                return $class->liveClass;
            })->map(function ($class) {
                return [
                    'id' => $class->liveClass->id,
                    'name' => $class->name,
                    'description' => $class->description,
                    'thumbnail' => $class->thumbnail ? asset('storage/class/thumbnail/' . $class->thumbnail) : asset('storage/class/thumbnail/default.png'),
                    'tutor' => $class->tutor->user->name
                ];
            })->toArray();
        }
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
            'courseWorks' => $array['courseWorks'],
            'liveClasses' => $array['liveClasses'],
        ] : [];
    }
}
