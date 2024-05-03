<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $permission_id
 * @property integer $role_id
 * @property Permission $permission
 * @property Role $role
 */
class RoleHasPermission extends Model
{
    /**
     * @var array
     */
    protected $fillable = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
