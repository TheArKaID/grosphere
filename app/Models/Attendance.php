<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * @property integer $id
 * @property integer $student_id
 * @property string $guardian
 * @property string $temperature
 * @property string $remark
 * @property string $type
 * @property string $proof
 * @property string $created_at
 * @property string $updated_at
 * @property Student $student
 */
class Attendance extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['student_id', 'guardian', 'temperature', 'remark', 'type', 'proof', 'created_at', 'updated_at'];

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
}
