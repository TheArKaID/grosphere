<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $course_work_id
 * @property integer $teacher_id
 * @property string $title
 * @property string $description
 * @property string $date
 * @property string $time
 * @property integer $quota
 * @property string $created_at
 * @property string $updated_at
 * @property ClassMaterial[] $classMaterials
 * @property CourseWork $courseWork
 * @property Teacher $teacher
 * @property StudentClass[] $studentClasses
 */
class ClassSession extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['course_work_id', 'teacher_id', 'title', 'description', 'date', 'time', 'quota', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function classMaterials()
    {
        return $this->hasMany(ClassMaterial::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function courseWork()
    {
        return $this->belongsTo(CourseWork::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function studentClasses()
    {
        return $this->hasMany(StudentClass::class);
    }
}
