<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Reverb\Loggers\Log;

/**
 * @property string $id
 * @property string $sender_id
 * @property string $recipient_id
 * @property string $recipient_group_id
 * @property string $message
 * @property bool $is_read 
 * @property string $created_at
 * @property string $updated_at
 * @property User $user
 * @property User $user
 */
class Message extends Model
{
    use HasUuids;

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     * 
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $fillable = ['sender_id', 'recipient_id', 'recipient_group_id', 'message', 'is_read', 'created_at', 'updated_at'];

    /**
     * Get the recipientUser that owns the Message
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function recipientUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    /**
     * Get the recipientGroup that owns the Message
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function recipientGroup(): BelongsTo
    {
        return $this->belongsTo(ClassGroup::class, 'recipient_group_id');
    }

    public function getRecipient()
    {
        if ($this->recipient_group_id) {
            return $this->recipientGroup;
        } else {
            return $this->recipientUser;
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
    
    /**
     * Boot
     */
    protected static function boot()
    {
        parent::boot();

        if (auth()->check() && !auth()->user()->hasRole('superadmin')) {
            static::addGlobalScope('agency', function ($builder) {
                $builder->whereHas('sender', function ($query) {
                    $query->where('agency_id', auth()->user()->agency_id);
                })->orWhere(function ($query) {
                    $query->whereHas('recipientUser', function ($query) {
                        $query->where('agency_id', auth()->user()->agency_id);
                    })->orWhereHas('recipientGroup', function ($query) {
                        $query->where('agency_id', auth()->user()->agency_id);
                    });;
                });
            });
        }
    }
}
