<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $course_student_id
 * @property string $student_id
 * @property string $class_session_id
 * @property string $present_at
 * @property string $rating
 * @property string $remark
 * @property string $created_at
 * @property string $updated_at
 * @property ClassSession $class
 * @property CourseStudent $courseStudent
 */
class StudentClass extends Pivot
{
    use HasUuids;

    protected $table = 'student_classes';
    /**
     * @var array
     */
    protected $fillable = ['course_student_id', 'student_id', 'class_session_id', 'present_at', 'rating', 'remark', 'created_at', 'updated_at'];

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

    /**
     * Get the student that owns the StudentClass
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
