<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $student_id
 * @property string $guardian
 * @property string $temperature
 * @property string $remark
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
    protected $fillable = ['student_id', 'guardian', 'temperature', 'remark', 'proof', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
