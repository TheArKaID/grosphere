<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $agenda_id
 * @property int $user_id
 * @property string $detail
 * @property string $date
 * @property integer $type
 */
class Calendar extends Model
{
    public static $TYPE_AGENDA = 1;
    public static $TYPE_LIVE_CLASS = 2;

    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'calendar_view';

    /**
     * @var array
     */
    protected $fillable = ['agenda_id', 'user_id', 'detail', 'date', 'type'];

    /**
     * Parse type to string
     * 
     * @return string
     */
    public function getTypeAttribute($type)
    {
        switch ($type) {
            case self::$TYPE_AGENDA:
                return 'Agenda';
            case self::$TYPE_LIVE_CLASS:
                return 'LiveClass';
            default:
                return 'Unknown';
        }
    }
}
