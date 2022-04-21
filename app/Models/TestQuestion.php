<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $chapter_test_id
 * @property string $question
 * @property integer $type
 * @property integer $answer_number
 * @property string $created_at
 * @property string $updated_at
 * @property ChapterTest $chapterTest
 * @property TestAnswer[] $testAnswers
 */
class TestQuestion extends Model
{
    public static $MULTIPLE_CHOICE = 1;
    public static $ESSAY = 2;
    
    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['chapter_test_id', 'question', 'type', 'answer_number', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function chapterTest()
    {
        return $this->belongsTo(ChapterTest::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function testAnswers()
    {
        return $this->hasMany(TestAnswer::class);
    }

    /**
     * Delete on boot
     * 
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($testQuestion) {
            foreach ($testQuestion->testAnswers as $ta) {
                $ta->delete();
            }
        });
    }
}
