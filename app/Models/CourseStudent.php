<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string $course_work_id
 * @property string $student_id
 * @property boolean $status
 * @property integer $type
 * @property string $created_at
 * @property string $updated_at
 * @property CourseWork $courseWork
 * @property Student $student
 */
class CourseStudent extends Model
{
    use HasUuids;

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
    protected $keyType = 'string';

    /**
     * @var array
     */
    protected $fillable = ['course_work_id', 'student_id', 'subscription_id',  'status', 'type', 'created_at', 'updated_at'];

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

    // /**
    //  * Get all of the askAnswers for the CourseStudent
    //  *
    //  * @return \Illuminate\Database\Eloquent\Relations\HasMany
    //  */
    // public function askAnswers(): HasMany
    // {
    //     return $this->hasMany(AskAnswer::class);
    // }

    // /**
    //  * Get all of the courseChapterStudent for the CourseStudent
    //  *
    //  * @return \Illuminate\Database\Eloquent\Relations\HasMany
    //  */
    // public function courseChapterStudent(): HasMany
    // {
    //     return $this->hasMany(CourseChapterStudent::class);
    // }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subscription()
    {
        return $this->belongsTo('App\Models\Subscription');
    }

    /**
     * Get all of the studentClasses for the Student
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function studentClasses(): HasMany
    {
        return $this->hasMany(StudentClass::class);
    }

    /**
     * Delete on boot
     * 
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($courseStudent) {
            $courseStudent->askAnswers->each->delete();
            
            $courseStudent->courseChapterStudent->each->delete();
        });
    }
}
