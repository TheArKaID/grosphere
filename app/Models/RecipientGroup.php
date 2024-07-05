<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property string $id
 * @property string $user_id
 * @property string $name
 * @property string $description
 * @property string $created_at
 * @property string $updated_at
 * @property RecipientGroupUser[] $recipientGroupUsers
 * @property User $user
 */
class RecipientGroup extends Model
{
    use HasUuids;

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     * 
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $fillable = ['user_id', 'name', 'description', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    /**
     * The Users that belong to the RecipientGroup
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function recipientUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->using(RecipientGroupUser::class)->withTimestamps();
    }

    /**
     * The Class Groups that belong to the RecipientGroup
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function recipientGroups(): BelongsToMany
    {
        return $this->belongsToMany(ClassGroup::class)->using(ClassGroupRecipientGroup::class)->withTimestamps();
    }

    function getRecipients(): array {
        return $this->recipientUsers->merge($this->recipientGroups->flatten()->toArray());
    }

    /**
     * Boot
     */
    protected static function boot()
    {
        parent::boot();

        if (auth()->check() && !auth()->user()->hasRole('superadmin')) {
            static::addGlobalScope('agency', function ($builder) {
                $builder->whereHas('sender', function ($query) {
                    $query->where('agency_id', auth()->user()->agency_id);
                });
            });
        }
    }
}
