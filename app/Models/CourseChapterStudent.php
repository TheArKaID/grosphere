<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $course_chapter_id
 * @property integer $course_student_id
 * @property boolean $status
 * @property string $created_at
 * @property string $updated_at
 * @property CourseChapter $courseChapter
 * @property CourseStudent $courseStudent
 * @property StudentAssignment[] $studentAssignments
 */
class CourseChapterStudent extends Model
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
    protected $fillable = ['course_chapter_id', 'course_student_id', 'status', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function courseChapter()
    {
        return $this->belongsTo(CourseChapter::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function courseStudent()
    {
        return $this->belongsTo(CourseStudent::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function studentAssignments()
    {
        return $this->hasMany(StudentAssignment::class);
    }
}
