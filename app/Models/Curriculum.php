<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $subject
 * @property integer $grade
 * @property integer $term
 * @property string $created_at
 * @property string $updated_at
 * @property Chapter[] $chapters
 * @property CourseWork[] $courseWorks
 */
class Curriculum extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['subject', 'grade', 'term', 'created_at', 'updated_at'];

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
}
