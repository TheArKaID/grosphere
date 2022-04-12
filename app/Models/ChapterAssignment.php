<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * @property integer $id
 * @property integer $course_chapter_id
 * @property string $task
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
    protected $fillable = ['course_chapter_id', 'task', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function courseChapter()
    {
        return $this->belongsTo(CourseChapter::class);
    }

    /**
     * Return all assignments files path
     * 
     * @return array
     */
    public function getFilesPath()
    {
        $path = [];
        foreach (Storage::cloud()->allFiles('course_works/' . $this->courseChapter->courseWork->id . '/chapters/' . $this->courseChapter->id . '/assignments') as $file) {
            array_push($path, Storage::cloud()->url($file));
        }
        return $path;
    }

    /**
     * Delete on boot
     * 
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            foreach (Storage::cloud()->allFiles('course_works/' . $model->courseChapter->courseWork->id . '/chapters/' . $model->courseChapter->id . '/assignments') as $file) {
                Storage::cloud()->delete($file);
            }
        });
    }
}
