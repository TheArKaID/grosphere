<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $guardian_id
 * @property integer $student_id
 * @property string $created_at
 * @property string $updated_at
 * @property Guardian $guardian
 * @property Student $student
 */
class GuardianStudent extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['guardian_id', 'student_id', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function guardian()
    {
        return $this->belongsTo(Guardian::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
