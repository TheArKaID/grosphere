<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property integer $id
 * @property integer $class_id
 * @property integer $course_category_id
 * @property string $published_at
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 * @property Class $class
 */
class CourseWork extends Model
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
    protected $fillable = ['class_id', 'course_category_id', 'deleted_at', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    /**
     * Get the courseCategory that owns the CourseWork
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function courseCategory(): BelongsTo
    {
        return $this->belongsTo(CourseCategory::class);
    }

    /**
     * Get all of the courseChapters for the CourseWork
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function courseChapters(): HasMany
    {
        return $this->hasMany(CourseChapter::class);
    }

    /**
     * Get all of the courseStudents for the CourseWork
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function courseStudents(): HasMany
    {
        return $this->hasMany(CourseStudent::class);
    }

    /**
     * Boot on deleting
     */
    public static function boot()
    {
        parent::boot();

        static::deleting(function ($courseWork) {
            foreach ($courseWork->courseChapters as $courseChapter) {
                $courseChapter->delete();
            }
            foreach ($courseWork->courseStudents as $cs) {
                $cs->delete();
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
            $builder->whereHas('class');
        });
    }
}
