<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property integer $id
 * @property integer $course_work_id
 * @property integer $student_id
 * @property boolean $status
 * @property integer $type
 * @property string $created_at
 * @property string $updated_at
 * @property CourseWork $courseWork
 * @property Student $student
 */
class CourseStudent extends Model
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
    protected $fillable = ['course_work_id', 'student_id', 'status', 'type', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function courseWork()
    {
        return $this->belongsTo(CourseWork::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get all of the askAnswers for the CourseStudent
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function askAnswers(): HasMany
    {
        return $this->hasMany(AskAnswer::class);
    }
}
