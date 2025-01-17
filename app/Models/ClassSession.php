<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property string $id
 * @property string $course_work_id
 * @property string $teacher_id
 * @property string $title
 * @property string $description
 * @property string $date
 * @property string $time
 * @property integer $quota
 * @property string $summary
 * @property string $type
 * @property string $thumbnail
 * @property string $created_at
 * @property string $updated_at
 * @property ClassMaterial[] $classMaterials
 * @property CourseWork $courseWork
 * @property Teacher $teacher
 * @property StudentClass[] $studentClasses
 */
class ClassSession extends Model
{
    use HasUuids;

    /**
     * @var array
     */
    protected $fillable = ['course_work_id', 'teacher_id', 'title', 'description', 'date', 'time', 'quota', 'summary', 'type', 'thumbnail', 'created_at', 'updated_at'];

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

    /**
     * The students that belong to the ClassSession
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'student_classes')->using(StudentClass::class)->withTimestamps();
    }

    /**
     * The students that belong to the ClassSession
     * 
     * @return mixed
     */
    public function enrolled()
    {
        return count($this->studentClasses) ? $this->studentClasses : $this->students;
    }

    /**
     * Boot
     */
    protected static function boot()
    {
        parent::boot();

        if (auth()->check() && !auth()->user()->hasRole('superadmin')) {
            static::addGlobalScope('agency', function ($builder) {
                $builder->whereHas('courseWork', function ($query) {
                    $query->where('agency_id', auth()->user()->agency_id);
                })->orWhereHas('teacher.user', function ($query) {
                    $query->where('agency_id', auth()->user()->agency_id);
                });
            });
        }
    }
}
