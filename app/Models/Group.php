<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $name
 * @property string $created_at
 * @property string $updated_at
 * @property GroupAccessClassess[] $groupAccessClassesses
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
    public function groupAccessClassesses()
    {
        return $this->hasMany(GroupAccessClasses::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function groupStudents()
    {
        return $this->hasMany(GroupStudent::class);
    }
}
