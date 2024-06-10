<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer $id
 * @property integer $agency_id
 * @property integer $curriculum_id
 * @property string $subject
 * @property integer $grade
 * @property integer $term
 * @property integer $quota
 * @property string $thumbnail
 * @property string $created_at
 * @property string $updated_at
 * @property ClassSession[] $classSessions
 * @property CourseStudent[] $courseStudents
 * @property CourseTeacher[] $courseTeachers
 * @property Curriculum $curriculum
 * @property Subscription[] $subscriptions
 * @property Agency $agency
 */
class CourseWork extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['curriculum_id', 'subject', 'grade', 'term', 'quota', 'thumbnail', 'created_at', 'updated_at'];

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

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    /**
     * Boot
     */
    protected static function boot()
    {
        parent::boot();

        if (auth()->check() && auth()->user()->role != 'superadmin') {
            static::addGlobalScope('agency', function ($builder) {
                $builder->where('agency_id', auth()->user()->agency_id);
            });
        }
    }
}
