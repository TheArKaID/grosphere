<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $name
 * @property string $readable_name
 * @property string $guard_name
 * @property string $created_at
 * @property string $updated_at
 * @property ModelHasRole[] $modelHasRoles
 * @property Permission[] $permissions
 */
class Role extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['name', 'readable_name', 'guard_name', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function modelHasRoles()
    {
        return $this->hasMany(ModelHasRole::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_has_permissions');
    }
}
