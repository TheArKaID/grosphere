<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $live_class_id
 * @property boolean $mic_on
 * @property boolean $cam_on
 * @property string $created_at
 * @property string $updated_at
 * @property LiveClass $liveClass
 */
class LiveClassSetting extends Model
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
    protected $fillable = ['live_class_id', 'mic_on', 'cam_on', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function liveClass()
    {
        return $this->belongsTo(LiveClass::class);
    }
}
