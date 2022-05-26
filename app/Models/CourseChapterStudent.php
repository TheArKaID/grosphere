<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property integer $id
 * @property integer $course_chapter_id
 * @property integer $course_student_id
 * @property boolean $status
 * @property string $created_at
 * @property string $updated_at
 * @property CourseChapter $courseChapter
 * @property CourseStudent $courseStudent
 * @property StudentAssignment $studentAssignment
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
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function studentAssignment()
    {
        return $this->hasOne(StudentAssignment::class);
    }

    /**
     * Get all of the studentTests for the CourseChapterStudent
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function studentTests(): HasMany
    {
        return $this->hasMany(StudentTest::class);
    }

    /**
     * Get latest student test
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function latestStudentTest(): HasOne
    {
        return $this->hasOne(StudentTest::class)->latest();
    }

    /**
     * Boot on deleting
     * 
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::deleting(function ($courseChapterStudent) {
            foreach ($courseChapterStudent->studentTests as $studentTest) {
                $studentTest->delete();
            }

            if ($courseChapterStudent->studentAssignment) {
                $courseChapterStudent->studentAssignment->delete();
            }
        });
    }
}
