<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $feed_id
 * @property string $url
 * @property string $content_type
 * @property string $file_path
 * @property string $file_name
 * @property string $file_extension
 * @property string $file_size
 * @property string $created_at
 * @property string $updated_at
 * @property Feed $feed
 */
class FeedImage extends Model
{
    use HasUuids;

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     * 
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $fillable = ['feed_id', 'url', 'content_type', 'file_path', 'file_name', 'file_extension', 'file_size', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function feed()
    {
        return $this->belongsTo(Feed::class);
    }
}
