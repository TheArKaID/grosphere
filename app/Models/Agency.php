<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $name
 * @property string $key
 * @property string $phone
 * @property string $email
 * @property string $address
 * @property string $website
 * @property string $about
 * @property string $sub_title
 * @property string $color
 * @property string $created_at
 * @property string $updated_at
 * @property Class[] $classes
 * @property User[] $users
 */
class Agency extends Model
{
    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['name', 'key', 'phone', 'email', 'address', 'website', 'about', 'sub_title', 'color', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function classes()
    {
        return $this->hasMany(Classes::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
