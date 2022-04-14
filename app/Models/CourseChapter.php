<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property integer $id
 * @property integer $course_work_id
 * @property string $title
 * @property string $description
 * @property string $content
 * @property boolean $order
 * @property boolean $status
 * @property string $created_at
 * @property string $updated_at
 * @property CourseWork $courseWork
 * @property ChapterAssignment $chapterAssignments
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
    protected $fillable = ['course_work_id', 'title', 'description', 'content', 'order', 'status', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function courseWork()
    {
        return $this->belongsTo(CourseWork::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function chapterAssignments()
    {
        return $this->hasOne(ChapterAssignment::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function courseChapterStudents()
    {
        return $this->hasMany(CourseChapterStudent::class);
    }

    /**
     * Get all of the chapterMaterial for the CourseChapter
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function chapterMaterial(): HasMany
    {
        return $this->hasMany(ChapterMaterial::class);
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
            if ($model->chapterAssignments) {
                $model->chapterAssignments->delete();
            }

            foreach ($model->courseChapterStudents as $ccs) {
                $ccs->delete();
            }
            foreach ($model->chapterMaterial as $cm) {
                $cm->delete();
            }
        });
    }
}
