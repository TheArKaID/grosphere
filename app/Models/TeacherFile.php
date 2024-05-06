<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $teacher_id
 * @property string $created_at
 * @property string $updated_at
 * @property Teacher $teacher
 */
class TeacherFile extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['teacher_id', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}