<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * @property string $id
 * @property string $tag_id
 * @property string $student_id
 * @property string $guardian_id
 * @property string $from_date
 * @property string $to_date
 * @property string $status
 * @property string $reason
 * @property string $created_at
 * @property string $updated_at
 * @property Guardian $guardian
 * @property Student $student
 * @property TagLeaveRequest $tagLeaveRequest
 */
class LeaveRequest extends Model
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
    protected $fillable = ['tag_id', 'student_id', 'guardian_id', 'from_date', 'to_date', 'status', 'reason', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function guardian(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Guardian::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function student(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function leaveRequestTag(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(LeaveRequestTag::class, 'tag_id');
    }

    /**
     * Get the tag associated with the LeaveRequest
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function tag(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(LeaveRequestTag::class, 'id', 'tag_id');
    }

    /**
     * Boot on delete
     * Delete S3 Storage
     * 
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        if (auth()->check() && !auth()->user()->hasRole('superadmin')) {
            static::addGlobalScope('agency', function ($builder) {
                $builder->whereHas('student', function ($query) {
                    $query->whereHas('user', function ($query) {
                        $query->where('agency_id', auth()->user()->agency_id);
                    });
                });
            });
        }

        static::deleting(function ($leaveRequest) {
            Storage::disk('s3')->delete('leave-requests/' . $leaveRequest->id . '.png');
        });
    }
}
