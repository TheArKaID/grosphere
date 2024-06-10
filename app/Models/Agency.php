<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property integer $id
 * @property string $name
 * @property string $phone
 * @property string $email
 * @property string $address
 * @property string $website
 * @property string $about
 * @property string $sub_title
 * @property string $color
 * @property string $status
 * @property string $active_until
 * @property string $created_at
 * @property string $updated_at
 * @property CourseWork[] $courseWorks
 * @property Curriculum[] $curriculas
 * @property User[] $users
 */
class Agency extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['name', 'phone', 'email', 'address', 'website', 'about', 'sub_title', 'color', 'status', 'active_until', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function courseWorks()
    {
        return $this->hasMany(CourseWork::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function curriculas()
    {
        return $this->hasMany(Curriculum::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Boot on delete
     * 
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($agency) {
            $agency->courseWorks()->delete();
            $agency->curriculas()->delete();
            $agency->users()->delete();
        });
    }
}
