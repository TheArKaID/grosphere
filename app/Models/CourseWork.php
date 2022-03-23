<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer $id
 * @property integer $class_id
 * @property integer $course_subject_id
 * @property int $duration
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
    protected $fillable = ['class_id', 'course_category_id', 'duration', 'deleted_at', 'created_at', 'updated_at'];

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
}
