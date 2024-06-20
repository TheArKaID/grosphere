<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @property string $id
 * @property string $user_id
 * @property string $address
 * @property string $created_at
 * @property string $updated_at
 * @property User $user
 * @property GuardianStudent[] $students
 */
class Guardian extends Model
{
    use HasUuids;

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
     * The students that belong to the Guardian
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'guardian_students')->using(GuardianStudent::class)->withTimestamps();
    }

    /**
     * Get all of the attendances for the Guardian
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
    /**
     * Boot on delete
     * 
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($guardian) {
            $guardian->students()->detach();
        });
        
        if (auth()->check() && !auth()->user()->hasRole('superadmin')) {
            static::addGlobalScope('agency', function ($builder) {
                $builder->whereHas('user', function ($query) {
                    $query->where('agency_id', auth()->user()->agency_id);
                });
            });
        }
    }
}
