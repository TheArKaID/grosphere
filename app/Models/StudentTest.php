<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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
     * Get Status
     * 
     * @return string
     */
    public function getStatus()
    {
        if ($this->status == self::$WORKING) {
            return 'Working';
        } elseif ($this->status == self::$SUBMITTED) {
            return 'Submitted';
        }
    }

    /**
     * Get Score (if submitted)
     * 
     * @return string
     */
    public function getScore()
    {
        if ($this->status == self::$SUBMITTED) {
            // format score
            return number_format($this->score, 2);
        }
    }

    /**
     * Return Submitted Test file path
     * 
     * @return array
     */
    public function getAnswerFilePath()
    {
        foreach (Storage::cloud()->allFiles('course_works/' . $this->courseChapterStudent->courseChapter->courseWork->id . '/chapters/' . $this->courseChapterStudent->courseChapter->id . '/tests_answers') as $file) {
            if (pathinfo($file, PATHINFO_FILENAME) == $this->courseChapterStudent->courseStudent->student_id) {
                return [
                    'file' => Storage::cloud()->url($file),
                    'shown_filename' => pathinfo($file, PATHINFO_FILENAME),
                    'extension' => pathinfo($file, PATHINFO_EXTENSION),
                ];
            }
        }
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
