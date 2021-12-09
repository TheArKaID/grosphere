<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property string $email_verified_at
 * @property string $password
 * @property boolean $status
 * @property string $remember_token
 * @property string $created_at
 * @property string $updated_at
 * @property Admin[] $admins
 * @property Parent[] $parents
 * @property Student[] $students
 * @property Tutor[] $tutors
 */
class User extends Authenticatable
{
    use HasRoles, HasApiTokens, HasFactory, Notifiable;

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['name', 'email', 'phone', 'email_verified_at', 'password', 'status', 'remember_token', 'created_at', 'updated_at'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * User has One Detail
     * 
     * @return HasOne
     */
    public function detail()
    {
        $detail = null;

        switch ($this->roles()->first()->name) {
            case 'admin':
                $detail = $this->hasOne(Admin::class);
                break;
            case 'parent':
                $detail = $this->hasOne(Parent::class);
                break;
            case 'student':
                $detail = $this->hasOne(Student::class);
                break;
            case 'tutor':
                $detail = $this->hasOne(Tutor::class);
                break;
            default:
                $detail = $this->hasOne(Student::class);
                break;
        }

        return $detail;
    }
}
