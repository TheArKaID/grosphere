<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property integer $id
 * @property integer $agency_id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $username
 * @property string $phone
 * @property string $email_verified_at
 * @property string $password
 * @property boolean $status
 * @property string $remember_token
 * @property string $created_at
 * @property string $updated_at
 * @property Admin[] $admins
 * @property Guardian[] $guardians
 * @property Student[] $students
 * @property Teacher[] $teachers
 * @property Agency $agency
 */
class User extends Authenticatable implements JWTSubject
{
    use HasRoles, HasFactory, Notifiable, HasUuids;

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['agency_id', 'first_name', 'last_name', 'email', 'username', 'phone', 'email_verified_at', 'password', 'status', 'remember_token', 'created_at', 'updated_at'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    // /**
    //  * @return \Illuminate\Database\Eloquent\Relations\HasMany
    //  */
    // public function liveUsers(): HasMany
    // {
    //     return $this->hasMany(LiveUser::class);
    // }

    /**
     * Get all of the announcementUsers for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function announcementUsers(): HasMany
    {
        return $this->hasMany(AnnouncementUser::class);
    }

    /**
     * Get all of the agendas for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function agendas(): HasMany
    {
        return $this->hasMany(Agenda::class);
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * User has One Detail
     * 
     * @return HasOne
     */
    public function detail()
    {
        $detail = null;

        switch ($this->roles()->first()?->name) {
            case 'admin':
                $detail = $this->hasOne(Admin::class);
                break;
            case 'guardian':
                $detail = $this->hasOne(Guardian::class);
                break;
            case 'student':
                $detail = $this->hasOne(Student::class);
                break;
            case 'teacher':
                $detail = $this->hasOne(Teacher::class);
                break;
            case 'superadmin':
                $detail = $this->hasOne(SuperAdmin::class);
                break;
            default:
                $detail = $this->hasOne(Student::class);
                break;
        }

        return $detail;
    }

    /**
     * Get detail of user for http response
     * 
     * @return array
     */
    public function getDetail()
    {
        $detail = null;

        switch ($this->roles()->first()->name) {
            case 'admin':
                $detail = [
                    'id' => $this->detail->id,
                    'phone' => $this->phone ?? 0
                ];
                break;
            case 'guardian':
                $detail = [
                    'id' => $this->detail->id,
                    'address' => $this->detail->address,
                    'phone' => $this->phone ?? 0
                ];
                break;
            case 'student':
                $detail = [
                    'id' => $this->detail->id,
                    'birth_place' => $this->detail->birth_place,
                    'birth_date' => $this->detail->birth_date,
                    'gender' => $this->detail->gender,
                    'address' => $this->detail->address,
                    'phone' => $this->phone ?? 0
                ];
                break;
            case 'teacher':
                $detail = [
                    'id' => $this->detail->id,
                    'phone' => $this->phone ?? 0
                ];
                break;
            default:
                $detail = $this->detail;
                break;
        }

        return $detail;
    }

    /**
     * Get the agency that owns the User
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    /**
     * Scope a query to only include users of logged in user's agency
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAgency($query)
    {
        return $query->where('agency_id', auth()->user()->agency_id);
    }

    /**
     * Delete detail on delete user
     * 
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::deleting(function ($user) {
        });
    }
}
