<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * @property integer $id
 * @property integer $student_id
 * @property integer $guardian_id
 * @property string $temperature
 * @property string $remark
 * @property string $type
 * @property string $proof
 * @property string $admin_id
 * @property string $created_at
 * @property string $updated_at
 * @property Student $student
 */
class Attendance extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['student_id', 'guardian_id', 'temperature', 'remark', 'type', 'proof', 'admin_id', 'created_at', 'updated_at'];

    protected $appends = ['out'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function getProofAttribute($value)
    {
        return $value ? Storage::disk('s3')->url($value) : null;
    }

    /**
     * Get the guardian that owns the Attendance
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function guardian(): BelongsTo
    {
        return $this->belongsTo(Guardian::class);
    }

    /**
     * Add "out" attribute to the model.
     * 
     * @param string $value
     * 
     * @return void
     */
    public function getOutAttribute()
    {
        return DB::table('attendances')
            ->select('id', 'student_id', 'type', 'created_at')
            ->where('student_id', $this->student_id)
            ->where('type', 'out')
            ->whereDate('created_at', $this->created_at)
            ->first()?->created_at;
    }

    /**
     * Get the admin that owns the Attendance
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }
}
