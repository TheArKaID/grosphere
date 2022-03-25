<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $course_work_id
 * @property string $title
 * @property string $content
 * @property boolean $order
 * @property boolean $status
 * @property string $created_at
 * @property string $updated_at
 * @property CourseWork $courseWork
 * @property ChapterAssignment[] $chapterAssignments
 * @property CourseChapterStudent[] $courseChapterStudents
 */
class CourseChapter extends Model
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
    protected $fillable = ['course_work_id', 'title', 'content', 'order', 'status', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function courseWork()
    {
        return $this->belongsTo(CourseWork::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function chapterAssignments()
    {
        return $this->hasMany(ChapterAssignment::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function courseChapterStudents()
    {
        return $this->hasMany(CourseChapterStudent::class);
    }
}
