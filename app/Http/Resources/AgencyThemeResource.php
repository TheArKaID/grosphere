<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class AgencyThemeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        parent::wrap('theme');

        return [
            'name' => $this->name,
            'website' => $this->website,
            'about' => $this->about,
            'sub_title' => $this->sub_title,
            'color' => $this->color,
            'logo' => Storage::disk('s3')->url('agencies/' . $this->id . '.png'),
        ];
    }
}
