<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $class_session_id
 * @property string $teacher_file_id
 * @property string $name
 * @property string $content
 * @property string $content_type
 * @property string $created_at
 * @property string $updated_at
 * @property ClassSession $classSession
 */
class ClassMaterial extends Model
{
    use HasUuids;

    /**
     * @var array
     */
    protected $fillable = ['class_session_id', 'teacher_file_id', 'name', 'content', 'content_type', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function classSession()
    {
        return $this->belongsTo(ClassSession::class);
    }

    /**
     * Get the detail that owns the ClassMaterial
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function detail(): BelongsTo
    {
        return $this->belongsTo(TeacherFile::class, 'teacher_file_id');
    }
}
