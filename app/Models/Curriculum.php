<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $agency_id
 * @property string $subject
 * @property integer $grade
 * @property integer $term
 * @property string $created_at
 * @property string $updated_at
 * @property Chapter[] $chapters
 * @property CourseWork[] $courseWorks
 * @property Agency $agency
 */
class Curriculum extends Model
{
    use HasUuids;

    /**
     * @var array
     */
    protected $fillable = ['agency_id', 'subject', 'grade', 'term', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function chapters()
    {
        return $this->hasMany(Chapter::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function courseWorks()
    {
        return $this->hasMany(CourseWork::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    /**
     * Boot
     */
    protected static function boot()
    {
        parent::boot();

        if (auth()->check() && !auth()->user()->hasRole('superadmin')) {
            static::addGlobalScope('agency', function ($builder) {
                $builder->where('agency_id', auth()->user()->agency_id);
            });
        }
    }
}
