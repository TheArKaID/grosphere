<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $name
 * @property string $created_at
 * @property string $updated_at
 * @property CourseWork[] $courseWorks
 */
class CourseCategory extends Model
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
    protected $fillable = ['name', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function courseWorks()
    {
        return $this->hasMany(CourseWork::class);
    }

    /**
     * Delete on boot
     * 
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($courseCategory) {
            foreach ($courseCategory->courseWorks as $cw) {
                $cw->delete();
            }
        });
    }
}
