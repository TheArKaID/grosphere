<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property integer $id
 * @property string $name
 * @property string $message
 * @property boolean $to
 * @property string $created_at
 * @property string $updated_at
 */
class Announcement extends Model
{
    /**
     * To Recipient
     */
    public static $ALL = 1;
    public static $STUDENT = 2;
    public static $TUTOR = 3;
    public static $PARENT = 4;

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['name', 'message', 'to', 'created_at', 'updated_at'];

    /**
     * Get all of the announcementUsers for the Announcement
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function announcementUsers(): HasMany
    {
        return $this->hasMany(announcementUsers::class);
    }

    /**
     * Get Recipient Name
     * 
     * @return string
     */
    public function getToName()
    {
        switch ($this->to) {
            case self::$ALL:
                return 'All';
            case self::$STUDENT:
                return 'Student';
            case self::$TUTOR:
                return 'Tutor';
            case self::$PARENT:
                return 'Parent';
            default:
                return 'Unknown';
        }
    }

    /**
     * boot on deleting
     * 
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::deleting(function ($announcement) {
            foreach ($announcement->announcementUsers as $au) {
                $au->delete();
            }
        });
    }
}
