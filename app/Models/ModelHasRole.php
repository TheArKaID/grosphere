<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $role_id
 * @property string $model_type
 * @property integer $model_id
 * @property Role $role
 */
class ModelHasRole extends Model
{
    /**
     * @var array
     */
    protected $fillable = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
