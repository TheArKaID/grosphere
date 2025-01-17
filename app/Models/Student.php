<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string $user_id
 * @property string $id_number
 * @property string $birth_date
 * @property string $birth_place
 * @property string $gender
 * @property string $address
 * @property string $created_at
 * @property string $updated_at
 * @property Guardian[] $guardians
 * @property User $user
 */
class Student extends Model
{
    use HasFactory, HasUuids;
    
    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'string';

    /**
     * @var array
     */
    protected $fillable = ['user_id', 'id_number', 'birth_date', 'birth_place', 'gender', 'address', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'birth_date' => 'datetime',
    ];
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function courseStudents()
    {
        return $this->hasMany(CourseStudent::class);
    }

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
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    // /**
    //  * Get all of the liveClassStudents for the Student
    //  *
    //  * @return \Illuminate\Database\Eloquent\Relations\HasMany
    //  */
    // public function liveClassStudents(): HasMany
    // {
    //     return $this->hasMany(LiveClassStudent::class);
    // }

    /**
     * The guardians that belong to the Student
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function guardians(): BelongsToMany
    {
        return $this->belongsToMany(Guardian::class, 'guardian_students')->using(GuardianStudent::class)->withTimestamps();
    }

    /**
     * The classes that belong to the Student
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(ClassSession::class, 'student_classes')->using(StudentClass::class)->withTimestamps();
    }

    /**
     * The classGroups that belong to the Student
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function classGroups(): BelongsToMany
    {
        return $this->belongsToMany(ClassGroup::class)->using(ClassGroupStudent::class)->withTimestamps();
    }

    /**
     * Get all of the attendances for the Student
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get all of the leaveRequests for the Student
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    /**
     * Boot
     */
    protected static function boot()
    {
        parent::boot();

        if (auth()->check() && !auth()->user()->hasRole('superadmin')) {
            static::addGlobalScope('agency', function ($builder) {
                $builder->whereHas('user', function ($query) {
                    $query->where('agency_id', auth()->user()->agency_id);
                });
            });
        }
    }
}
