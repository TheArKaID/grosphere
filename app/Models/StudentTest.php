<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $course_chapter_student_id
 * @property integer $status
 * @property float $score
 * @property string $created_at
 * @property string $updated_at
 * @property CourseChapterStudent $courseChapterStudent
 * @property StudentTestAnswer[] $studentTestAnswers
 */
class StudentTest extends Model
{
    public static $WORKING = 1;
    public static $SUBMITTED = 2;

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['course_chapter_student_id', 'status', 'score', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function courseChapterStudent()
    {
        return $this->belongsTo(CourseChapterStudent::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function studentTestAnswers()
    {
        return $this->hasMany(StudentTestAnswer::class);
    }

    /**
     * Boot on deleting
     * 
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::deleting(function ($studentTest) {
            foreach ($studentTest->studentTestAnswers as $sta) {
                $sta->delete();
            }
        });
    }
}
