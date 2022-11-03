<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $name
 * @property string $created_at
 * @property string $updated_at
 * @property GroupAccessClass[] $groupAccessClasses
 * @property GroupStudent[] $groupStudents
 */
class Group extends Model
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
    public function groupAccessClasses()
    {
        return $this->hasMany(GroupAccessClass::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function groupStudents()
    {
        return $this->hasMany(GroupStudent::class);
    }

    /**
     * Boot on delete
     * 
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($group) {
            $group->groupAccessClasses()->delete();
            
            foreach ($group->groupStudents as $gs) {
                $gs->delete();
            }

            foreach ($group->groupAccessClasses as $ga) {
                $ga->delete();
            }
        });
    }
}
