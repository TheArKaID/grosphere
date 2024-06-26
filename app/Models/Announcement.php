<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string $agency_id
 * @property string $title
 * @property string $content
 * @property boolean $status
 * @property string $created_at
 * @property string $updated_at
 */
class Announcement extends Model
{
    use HasUuids;

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'string';

    /**
     * @var array
     */
    protected $fillable = ['agency_id', 'title', 'content', 'status', 'created_at', 'updated_at'];

    /**
     * boot on deleting
     * 
     * @return void
     */
    public static function boot()
    {
        parent::boot();
    }
}
