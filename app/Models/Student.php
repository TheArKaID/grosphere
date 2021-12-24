<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $user_id
 * @property integer $parent_id
 * @property string $id_number
 * @property string $birth_date
 * @property string $birth_place
 * @property boolean $gender
 * @property string $address
 * @property string $created_at
 * @property string $updated_at
 * @property Parent $parent
 * @property User $user
 */
class Student extends Model
{
    use HasFactory;
    
    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['user_id', 'parent_id', 'id_number', 'birth_date', 'birth_place', 'gender', 'address', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(Parents::class, 'parent_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
