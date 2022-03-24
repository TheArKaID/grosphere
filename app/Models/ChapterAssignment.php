<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $course_chapter_id
 * @property string $task
 * @property string $file
 * @property string $created_at
 * @property string $updated_at
 * @property CourseChapter $courseChapter
 */
class ChapterAssignment extends Model
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
    protected $fillable = ['course_chapter_id', 'task', 'file', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function courseChapter()
    {
        return $this->belongsTo(CourseChapter::class);
    }
}
