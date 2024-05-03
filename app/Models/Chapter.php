<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $curriculum_id
 * @property string $title
 * @property string $description
 * @property string $content
 * @property string $created_at
 * @property string $updated_at
 * @property Curriculum $curriculum
 */
class Chapter extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['curriculum_id', 'title', 'description', 'content', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function curriculum()
    {
        return $this->belongsTo(Curriculum::class);
    }
}
