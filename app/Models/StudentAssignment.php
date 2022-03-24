<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $course_chapter_student_id
 * @property string $answer
 * @property string $file
 * @property string $created_at
 * @property string $updated_at
 * @property CourseChapterStudent $courseChapterStudent
 */
class StudentAssignment extends Model
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
    protected $fillable = ['course_chapter_student_id', 'answer', 'file', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function courseChapterStudent()
    {
        return $this->belongsTo(CourseChapterStudent::class);
    }
}
