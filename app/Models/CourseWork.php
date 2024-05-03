<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $curriculum_id
 * @property integer $duration
 * @property string $created_at
 * @property string $updated_at
 * @property ClassSession[] $classSessions
 * @property CourseStudent[] $courseStudents
 * @property CourseTeacher[] $courseTeachers
 * @property Curriculum $curriculum
 * @property Subscription[] $subscriptions
 */
class CourseWork extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['curriculum_id', 'duration', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function classSessions()
    {
        return $this->hasMany(ClassSession::class);
    }

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
    public function courseTeachers()
    {
        return $this->hasMany(CourseTeacher::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function curriculum()
    {
        return $this->belongsTo(Curriculum::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
