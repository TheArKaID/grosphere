<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $curriculum_id
 * @property string $name
 * @property string $description
 * @property string $content
 * @property string $content_type
 * @property string $created_at
 * @property string $updated_at
 * @property Curriculum $curriculum
 */
class Chapter extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['curriculum_id', 'name', 'description', 'content', 'content_type', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function curriculum()
    {
        return $this->belongsTo(Curriculum::class);
    }
}
