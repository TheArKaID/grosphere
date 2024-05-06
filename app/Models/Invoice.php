<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $subscription_id
 * @property string $invoice_number
 * @property string $invoice_file
 * @property integer $price
 * @property string $currency
 * @property integer $active_days
 * @property integer $total_meeting
 * @property string $due_date
 * @property string $expired_date
 * @property string $status
 * @property string $payment_method
 * @property string $payment_proof
 * @property string $created_at
 * @property string $updated_at
 * @property Subscription $subscription
 */
class Invoice extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['subscription_id', 'invoice_number', 'invoice_file', 'price', 'currency', 'active_days', 'total_meeting', 'due_date', 'expired_date', 'status', 'payment_method', 'payment_proof', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}