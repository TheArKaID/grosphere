<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * @property integer $id
 * @property integer $course_chapter_student_id
 * @property string $answer
 * @property float $score
 * @property string $created_at
 * @property string $updated_at
 * @property CourseChapterStudent $courseChapterStudent
 */
class StudentAssignment extends Model
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
    protected $fillable = ['course_chapter_student_id', 'answer', 'score', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function courseChapterStudent()
    {
        return $this->belongsTo(CourseChapterStudent::class);
    }

    /**
     * Return all assignments files path
     * 
     * @return array
     */
    public function getFilesPath()
    {
        foreach (Storage::cloud()->allFiles('course_works/' . $this->courseChapterStudent->courseChapter->courseWork->id . '/chapters/' . $this->courseChapterStudent->courseChapter->id . '/assignments_results/' . $this->id) as $file) {
            return [
                'file' => Storage::cloud()->url($file),
                'shown_filename' => 'dummy.pdf'
            ];
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

        static::deleting(function ($studentAssignment) {
            foreach (Storage::cloud()->allFiles('course_works/' . $studentAssignment->courseChapterStudent->courseChapter->courseWork->id . '/chapters/' . $studentAssignment->courseChapterStudent->courseChapter->id . '/assignments_results/' . $studentAssignment->id) as $file) {
                Storage::cloud()->delete($file);
            }
        });
    }
}
