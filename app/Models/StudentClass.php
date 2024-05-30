<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $course_student_id
 * @property integer $class_session_id
 * @property string $present_at
 * @property string $rating
 * @property string $remark
 * @property string $created_at
 * @property string $updated_at
 * @property ClassSession $class
 * @property CourseStudent $courseStudent
 */
class StudentClass extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['course_student_id', 'class_session_id', 'present_at', 'rating', 'remark', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function classSession()
    {
        return $this->belongsTo(ClassSession::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function courseStudent()
    {
        return $this->belongsTo(CourseStudent::class);
    }
}
