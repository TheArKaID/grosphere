<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $test_question_id
 * @property string $answer
 * @property integer $number
 * @property string $created_at
 * @property string $updated_at
 * @property TestQuestion $testQuestion
 */
class TestAnswer extends Model
{
    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['test_question_id', 'answer', 'number', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function testQuestion()
    {
        return $this->belongsTo(TestQuestion::class);
    }
}
