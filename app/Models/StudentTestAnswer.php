<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $student_test_id
 * @property integer $test_question_id
 * @property string $answer
 * @property boolean $is_correct
 * @property string $created_at
 * @property string $updated_at
 * @property StudentTest $studentTest
 * @property TestQuestion $testQuestion
 */
class StudentTestAnswer extends Model
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
    protected $fillable = ['student_test_id', 'test_question_id', 'answer', 'is_correct', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function studentTest()
    {
        return $this->belongsTo(StudentTest::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function testQuestion()
    {
        return $this->belongsTo(TestQuestion::class);
    }
}
