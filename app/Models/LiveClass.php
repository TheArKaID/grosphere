<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property integer $id
 * @property integer $class_id
 * @property int $duration
 * @property string $start_time
 * @property string $host_code
 * @property string $user_code
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 * @property Class $class
 */
class LiveClass extends Model
{
    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['class_id', 'duration', 'start_time', 'host_code', 'user_code', 'deleted_at', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'start_time' => 'datetime',
    ];
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    /**
     * Get all of the liveUsers for the LiveClass
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function liveUsers(): HasMany
    {
        return $this->hasMany(LiveUser::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($liveClass) {
            foreach ($liveClass->liveUsers as $liveUser) {
                $liveUser->delete();
            }
        });
    }
}
