<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $course_work_id
 * @property integer $teacher_id
 * @property boolean $status
 * @property string $created_at
 * @property string $updated_at
 * @property CourseWork $courseWork
 * @property Teacher $teacher
 */
class CourseTeacher extends Model
{
    use HasUuids;

    /**
     * @var array
     */
    protected $fillable = ['course_work_id', 'teacher_id', 'status', 'created_at', 'updated_at'];

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
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
