<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * @property integer $id
 * @property integer $course_chapter_id
 * @property string $shown_filename
 * @property string $saved_filename
 * @property string $ext
 * @property string $created_at
 * @property string $updated_at
 * @property CourseChapter $courseChapter
 */
class ChapterMaterial extends Model
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
    protected $fillable = ['course_chapter_id', 'shown_filename', 'saved_filename', 'ext', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function courseChapter()
    {
        return $this->belongsTo(CourseChapter::class);
    }

    /**
     * Return material file path
     * 
     * @return string|null
     */
    public function getFilePath()
    {
        return Storage::cloud()->url('course_works/' . $this->courseChapter->courseWork->id . '/chapters/' . $this->courseChapter->id . '/materials/' . $this->saved_filename);
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
            Storage::cloud()->delete('course_works/' . $model->courseChapter->courseWork->id . '/chapters/' . $model->courseChapter->id . '/materials/' . $model->saved_filename);
        });
    }
}
