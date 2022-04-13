<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * @property integer $id
 * @property integer $course_chapter_student_id
 * @property string $answer
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
    protected $fillable = ['course_chapter_student_id', 'answer', 'created_at', 'updated_at'];

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
        $path = [];
        foreach (Storage::cloud()->allFiles('course_works/' . $this->courseChapterStudent->courseChapter->courseWork->id . '/chapters/' . $this->courseChapterStudent->courseChapter->id . '/assignments_results/' . $this->id) as $file) {
            array_push($path, Storage::cloud()->url($file));
        }
        return $path;
    }
}
