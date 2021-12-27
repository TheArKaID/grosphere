<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $user_id
 * @property integer $live_class_id
 * @property string $time_in
 * @property string $time_out
 * @property int $entries
 * @property boolean $status
 * @property string $created_at
 * @property string $updated_at
 * @property LiveClass $liveClass
 * @property User $user
 */
class LiveUser extends Model
{
    public static $STATUS_IN = 1;
    public static $STATUS_OUT = 2;
    public static $STATUS_DENIED = 9;
    
    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['user_id', 'live_class_id', 'time_in', 'time_out', 'entries', 'status', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function liveClass()
    {
        return $this->belongsTo(LiveClass::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
