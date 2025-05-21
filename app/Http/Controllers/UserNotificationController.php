<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class UserNotificationController extends AppBaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|JsonResponse|View
     */
    public function index()
    {
        $notifications = UserNotification::whereUserId(\Auth::id())->where('read_at',
            null)->orderByDesc('created_at')->get();

        return $this->sendResponse($notifications, 'Notification retrieved successfully');
    }

    /**
     * @param  UserNotification  $notification
     * @return JsonResponse
     */
    public function readNotification(UserNotification $notification)
    {
        $notification->read_at = Carbon::now();
        $notification->save();

        return $this->sendSuccess('Notification read successfully.');
    }

    /**
     * @return JsonResponse
     */
    public function readAllNotification()
    {
        UserNotification::whereReadAt(null)->where('user_id',
            getLoggedInUserId())->update(['read_at' => Carbon::now()]);

        return $this->sendSuccess('All Notification read successfully.');
    }
}
