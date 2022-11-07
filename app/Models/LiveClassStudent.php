<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $live_class_id
 * @property integer $student_id
 * @property integer $type
 * @property string $created_at
 * @property string $updated_at
 * @property LiveClass $liveClass
 * @property Student $student
 */
class LiveClassStudent extends Model
{
    /**
     * The type of student enrollment
     */
    public static $PERSONAL = 1;
    public static $GROUP = 2;

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['live_class_id', 'student_id', 'type', 'created_at', 'updated_at'];

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
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
