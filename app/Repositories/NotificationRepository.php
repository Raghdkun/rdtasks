<?php

namespace App\Repositories;

use App\Notification;

/**
 * Class NotificationRepository
 */
class NotificationRepository
{
    /**
     * @param $input
     * @return mixed
     */
    public function editNotification($input)
    {
        $firstTime = $input['firstTime'];
        $secondTime = $input['secondTime'];
        $thirdTime = $input['thirdTime'];
        $userExists = Notification::where('user_id', '=', \Auth::id())->exists();
        if (! $userExists) {
            $notification = Notification::create([
                'user_id' => \Auth::id(),
                'first_notification_hour' => $firstTime,
                'second_notification_hour' => $secondTime,
                'third_notification_hour' => $thirdTime,
            ]);
        } else {
            $notification = Notification::where('user_id', '=', \Auth::id());
            $notification->update([
                'first_notification_hour' => $firstTime,
                'second_notification_hour' => $secondTime,
                'third_notification_hour' => $thirdTime,
            ]);
        }

        return $notification;
    }
}
