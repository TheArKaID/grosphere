<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @property integer $id
 * @property integer $user_id
 * @property string $address
 * @property string $created_at
 * @property string $updated_at
 * @property User $user
 * @property GuardianStudent[] $students
 */
class Guardian extends Model
{
    /**
     * Table name
     */
    protected $table = 'guardians';

    /**
     * @var array
     */
    protected $fillable = ['user_id', 'address', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function students()
    {
        return $this->hasMany(GuardianStudent::class);
    }
}
