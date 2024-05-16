<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        parent::wrap('invoices');
        return $this->resource ? [
            'id' => $this->id,
            'subscription_id' => $this->subscription_id,
            'invoice_number' => $this->invoice_number,
            'invoice_file' => $this->invoice_file,
            'currency' => $this->currency,
            'price' => $this->price,
            'total_meeting' => $this->total_meeting,
            'active_days' => $this->active_days,
            'due_date' => $this->due_date,
            'expired_date' => $this->expired_date,
            'status' => $this->status,
            'payment_method' => $this->payment_method,
            'created_at' => $this->created_at,
            'subscription' => SubscriptionResource::make($this->whenLoaded('subscription')),
        ] : [];
    }
}
