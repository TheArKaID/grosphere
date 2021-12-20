<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $tutor_id
 * @property string $name
 * @property string $description
 * @property string $thumbnail
 * @property boolean $type
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 * @property Tutor $tutor
 * @property CourseClass[] $courseClasses
 * @property LiveClass[] $liveClasses
 */
class Classes extends Model
{
    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * Course Type Class
     * 
     * @var int
     */
    public static $COURSE = 1;

    /**
     * Live Type Class
     * 
     * @var int
     */
    public static $LIVE = 2;


    /**
     * @var array
     */
    protected $fillable = ['tutor_id', 'name', 'description', 'thumbnail', 'type', 'deleted_at', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tutor()
    {
        return $this->belongsTo(Tutor::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function courseClasses()
    {
        return $this->hasMany(CourseClass::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function liveClasses()
    {
        return $this->hasMany(LiveClass::class);
    }
}
