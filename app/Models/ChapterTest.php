<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * @property integer $id
 * @property integer $course_chapter_id
 * @property string $title
 * @property int $duration
 * @property int $attempt
 * @property string $available_at
 * @property string $available_until
 * @property boolean $status
 * @property string $created_at
 * @property string $updated_at
 * @property CourseChapter $courseChapter
 * @property TestQuestion[] $testQuestions
 */
class ChapterTest extends Model
{
    public static $ON_APP = 1;
    public static $ON_FILE = 2;

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['course_chapter_id', 'title', 'duration', 'attempt', 'type', 'available_at', 'available_until', 'status', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'available_at' => 'datetime',
        'available_until' => 'datetime',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function courseChapter()
    {
        return $this->belongsTo(CourseChapter::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function testQuestions()
    {
        return $this->hasMany(TestQuestion::class);
    }

    /**
     * Get Uploaded Test File
     * 
     * @return string
     */
    public function getFile()
    {
        foreach (Storage::cloud()->allFiles('course_works/' . $this->courseChapter->courseWork->id . '/chapters/' . $this->courseChapter->id . '/tests') as $file) {
            return Storage::cloud()->url($file);
        }
    }
    /**
     * Delete on boot
     * 
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function($chapterTest) {
            foreach ($chapterTest->testQuestions as $tq) {
                $tq->delete();
            }
        });
    }
}