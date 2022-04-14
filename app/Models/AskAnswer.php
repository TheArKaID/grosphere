<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $course_student_id
 * @property integer $from
 * @property string $message
 * @property string $created_at
 * @property string $updated_at
 * @property CourseStudent $courseStudent
 */
class AskAnswer extends Model
{
    public static $FROM_STUDENT = 1;
    public static $FROM_TUTOR = 2;

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['course_student_id', 'from', 'message', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function courseStudent()
    {
        return $this->belongsTo(CourseStudent::class);
    }
}
