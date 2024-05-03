<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $chapter_id
 * @property string $name
 * @property string $content
 * @property string $content_type
 * @property string $created_at
 * @property string $updated_at
 * @property Chapter $chapter
 */
class ChapterMaterial extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['chapter_id', 'name', 'content', 'content_type', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }
}
