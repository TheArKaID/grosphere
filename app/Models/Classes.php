<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer $id
 * @property integer $tutor_id
 * @property integer $institute_id
 * @property string $name
 * @property string $description
 * @property string $thumbnail
 * @property boolean $type
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 * @property Tutor $tutor
 * @property CourseWork[] $courseWorks
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
    protected $fillable = ['tutor_id', 'institute_id', 'name', 'description', 'thumbnail', 'type', 'deleted_at', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tutor()
    {
        return $this->belongsTo(Tutor::class);
    }

    /**
     * Get the institute that owns the Classes
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function institute(): BelongsTo
    {
        return $this->belongsTo(Institute::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function courseWorks()
    {
        return $this->hasMany(CourseWork::class, 'class_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function liveClasses()
    {
        return $this->hasMany(LiveClass::class, 'class_id');
    }

    /**
     * delete course works and live classes on delete classes
     * 
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::deleting(function ($classes) {
            foreach ($classes->courseWorks as $courseWork) {
                $courseWork->delete();
            }

            foreach ($classes->liveClasses as $liveClass) {
                $liveClass->delete();
            }
        });
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope('institute', function (Builder $builder) {
            $builder->where('institute_id', '=', auth()->user()->institute_id);
        });
    }
}
