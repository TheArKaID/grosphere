<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $student_id
 * @property integer $course_work_id
 * @property string $currency
 * @property integer $price
 * @property integer $active_days
 * @property string $active_until
 * @property integer $total_meeting
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 * @property CourseStudent[] $courseStudents
 * @property Invoice[] $invoices
 * @property CourseWork $courseWork
 * @property Student $student
 */
class Subscription extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['student_id', 'course_work_id', 'price', 'currency', 'active_days', 'active_until', 'total_meeting', 'status', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function courseStudents()
    {
        return $this->hasMany(CourseStudent::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

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
}
