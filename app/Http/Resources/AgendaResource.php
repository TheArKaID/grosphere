<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class AgendaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        parent::wrap('agendas');

        return $this->resource ? [
            'id' => $this->id,
            'date' => Carbon::parse($this->date)->format('d-m-Y'),
            'detail' => $this->detail
        ] : [];
    }
}
