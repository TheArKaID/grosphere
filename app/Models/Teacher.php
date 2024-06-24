<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string $user_id
 * @property string $created_at
 * @property string $updated_at
 * @property ClassSession[] $classSessions
 * @property CourseTeacher[] $courseTeachers
 * @property User $user
 */
class Teacher extends Model
{
    use HasUuids;

    /**
     * @var array
     */
    protected $fillable = ['user_id', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function classSessions()
    {
        return $this->hasMany(ClassSession::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function courseTeachers()
    {
        return $this->hasMany(CourseTeacher::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all of the classGroups for the Teacher
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function classGroups(): HasMany
    {
        return $this->hasMany(ClassGroup::class);
    }

    /**
     * Boot
     */
    protected static function boot()
    {
        parent::boot();

        if (auth()->check() && !auth()->user()->hasRole('superadmin')) {
            static::addGlobalScope('agency', function ($builder) {
                $builder->whereHas('user', function ($query) {
                    $query->where('agency_id', auth()->user()->agency_id);
                });
            });
        }
    }
}
