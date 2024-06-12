<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $teacher_id
 * @property string $name
 * @property string $content
 * @property string $content_type
 * @property string $file_path
 * @property string $file_name
 * @property string $file_extension
 * @property string $file_size
 * @property string $created_at
 * @property string $updated_at
 * @property Teacher $teacher
 */
class TeacherFile extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['teacher_id', 'name', 'content', 'content_type', 'file_path', 'file_name', 'file_extension', 'file_size', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Parse the file_size from bytes to mega bytes
     * 
     * @return string
     */
    public function fileSize(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => round($value / 1024 / 1024, 2)
        );
    }
}
