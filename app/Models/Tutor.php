<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $user_id
 * @property string $created_at
 * @property string $updated_at
 * @property User $user
 */
class Tutor extends Model
{
    use HasFactory;
    
    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['user_id', 'created_at', 'updated_at'];

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
    public function classSessions()
    {
        return $this->hasMany(ClassSession::class);
    }

    /**
     * Delete classSessions on delete tutor
     * 
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::deleting(function ($tutor) {
            foreach ($tutor->classSessions as $class) {
                $class->delete();
            }
        });
    }
}
