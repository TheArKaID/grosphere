<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $uuid
 * @property string $connection
 * @property string $queue
 * @property string $payload
 * @property string $exception
 * @property string $failed_at
 */
class FailedJob extends Model
{
    use HasUuids;

    /**
     * @var array
     */
    protected $fillable = ['uuid', 'connection', 'queue', 'payload', 'exception', 'failed_at'];
}
