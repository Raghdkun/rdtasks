<?php

namespace App\Http\Controllers;

use App\Repositories\TimeEntryRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TimeEntryCalenderController extends AppBaseController
{
    /** @var TimeEntryRepository */
    private $timeEntryRepository;

    public function __construct(TimeEntryRepository $timeEntryRepo)
    {
        $this->timeEntryRepository = $timeEntryRepo;
    }

    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        $data = $this->timeEntryRepository->getTimeEntryData();

        return view('time_entries_calendars.index')->with($data);
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function timeEntriesCalendarList(Request $request)
    {
        $input = $request->all();
        $timeEntries = $this->timeEntryRepository->getTimeEntriesForCalenderView($input);

        return $this->sendResponse($timeEntries, 'Time Entries retrieved successfully.');
    }
}
