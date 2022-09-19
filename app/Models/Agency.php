<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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

    /**
     * Get Agency logo
     * 
     * @return string
     */
    public function getLogo()
    {
        if (Storage::cloud()->exists('agencies/' . $this->id . '/logo.png')) {
            return Storage::cloud()->url('agencies/' . $this->id . '/logo.png');
        } else {
            return null;
            // return Storage::cloud()->url('agency/logo.png');
        }
    }

    /**
     * Get Agency Small Logo
     * 
     * @return string
     */
    public function getLogoSmall()
    {
        if (Storage::cloud()->exists('agencies/' . $this->id . '/logo_small.png')) {
            return Storage::cloud()->url('agencies/' . $this->id . '/logo_small.png');
        } else {
            return null;
            // return Storage::cloud()->url('agency/logo_small.png');
        }
    }
}
