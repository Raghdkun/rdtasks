<?php

namespace App\Http\Controllers;

use App\Http\Requests\NotificationCreateRequest;
use App\Notification;
use App\Repositories\NotificationRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class NotificationController extends AppBaseController
{
    /**
     * @var NotificationRepository
     */
    private $notificationRepo;

    public function __construct(NotificationRepository $notificationRepository)
    {
        $this->notificationRepo = $notificationRepository;
    }

    /**
     * @return JsonResponse
     */
    public function index()
    {
        $data = Notification::where('user_id', '=', Auth::id())->first();

        return $this->sendResponse($data, 'Notification Retrieved Successfully.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  NotificationCreateRequest  $request
     * @return JsonResponse
     */
    public function store(NotificationCreateRequest $request)
    {
        $input = $request->all();
        $firstHour = explode(':', $input['firstTime']);
        $secondHour = explode(':', $input['secondTime']);
        $thirdHour = explode(':', $input['thirdTime']);
        if ($firstHour >= $secondHour) {
            return $this->sendError('Second notify time should greater than first ');
        } elseif ($secondHour >= $thirdHour) {
            return $this->sendError('Third notify time should greater than second');
        }
        if ($firstHour[0] >= 2) {
            return $this->sendError('You can set max 2 hrs for notification.');
        } elseif ($secondHour[0] >= 2) {
            return  $this->sendError('You can set max 2 hrs for notification.');
        } elseif ($thirdHour[0] >= 2) {
            return  $this->sendError('You can set max 2 hrs for notification.');
        } elseif ($firstHour[1] == 00) {
            return $this->sendError('first notification time should be greater than zero');
        }
        $data = $this->notificationRepo->editNotification($request->all());

        return $this->sendSuccess('Notification Save Successfully.');
    }
}
