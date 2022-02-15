<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $user_id
 * @property string $address
 * @property string $created_at
 * @property string $updated_at
 * @property User $user
 * @property Student[] $students
 */
class Parents extends Model
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
        return $this->hasMany(Student::class, 'parent_id');
    }

    /**
     * Delete students on delete parent
     * 
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::deleting(function ($parent) {
            foreach ($parent->students as $student) {
                $student->parent_id = null;
            }
        });
    }
}
