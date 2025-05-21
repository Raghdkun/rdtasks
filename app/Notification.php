<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Notification
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $first_notification_hour
 * @property string|null $second_notification_hour
 * @property string|null $third_notification_hour
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Notification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification query()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereFirstNotificationHour($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereSecondNotificationHour($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereThirdNotificationHour($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereUserId($value)
 * @mixin \Eloquent
 */
class Notification extends Model
{
    /**
     * @var string
     */
    protected $table = 'notifications';

    /**
     * @var string[]
     */
    protected $fillable = [
        'first_notification_hour',
        'second_notification_hour',
        'third_notification_hour',
        'user_id',
    ];

    /**
     * @var string[]
     */
    public static $rules = [
        'firstTime' => 'required',
        'secondTime' => 'required',
        'thirdTime' => 'required',
    ];
}
