<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTimeEntryRequest;
use App\Http\Requests\UpdateTimeEntryRequest;
use App\Models\Project;
use App\Models\TimeEntry;
use App\Queries\TimeEntryDataTable;
use App\Repositories\TimeEntryRepository;
use Auth;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;

/**
 * Class TimeEntryController.
 */
class TimeEntryController extends AppBaseController
{
    /** @var TimeEntryRepository */
    private $timeEntryRepository;

    public function __construct(TimeEntryRepository $timeEntryRepo)
    {
        $this->timeEntryRepository = $timeEntryRepo;
    }

    /**
     * Display a listing of the TimeEntry.
     *
     * @param  Request  $request
     * @return Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request)
    {
        $data = $this->timeEntryRepository->getTimeEntryData();

        return view('time_entries.index')->with($data);
    }

    /**
     * Store a newly created TimeEntry in storage.
     *
     * @param  CreateTimeEntryRequest  $request
     * @return JsonResponse
     */
    public function store(CreateTimeEntryRequest $request)
    {
        $this->timeEntryRepository->store($request->all());
        $this->timeEntryRepository->broadcastStopTimerEvent();

        return $this->sendSuccess('Time Entry created successfully.');
    }

    /**
     * Show the form for editing the specified TimeEntry.
     *
     * @param  TimeEntry  $timeEntry
     * @return JsonResponse
     */
    public function edit(TimeEntry $timeEntry)
    {
        $user = Auth::user();
        if (! $user->can('manage_projects') && $timeEntry->user_id != getLoggedInUserId()) {
            return $this->sendError('You are not allow to edit this entry.');
        }

        $timeEntryDetails = $this->timeEntryRepository->getTimeEntryDetail($timeEntry->id);

        return $this->sendResponse($timeEntryDetails, 'Time Entry retrieved successfully.');
    }

    /**
     * Update the specified TimeEntry in storage.
     *
     * @param  TimeEntry  $timeEntry
     * @param  UpdateTimeEntryRequest  $request
     * @return JsonResponse
     */
    public function update(TimeEntry $timeEntry, UpdateTimeEntryRequest $request)
    {
        $user = getLoggedInUser();
        if (! $user->can('manage_projects')) {
            $timeEntry = TimeEntry::ofCurrentUser()->find($timeEntry->id);
        }
        if (empty($timeEntry)) {
            return $this->sendError('Time Entry not found.', Response::HTTP_NOT_FOUND);
        }

        $input = $request->all();
        $this->timeEntryRepository->updateTimeEntry($input, $timeEntry->id);

        return $this->sendSuccess('Time Entry updated successfully.');
    }

    /**
     * @param  TimeEntry  $timeEntry
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(TimeEntry $timeEntry)
    {
        $user = Auth::user();

        if (! $user->can('manage_projects') && $timeEntry->user_id != getLoggedInUserId()) {
            return $this->sendError('You are not allow to delete this entry.');
//            throw new UnauthorizedException('You are not allow to delete this entry.', 402);
        }

        $timeEntry->update(['deleted_by' => getLoggedInUserId()]);
        $timeEntry->delete();

        return $this->sendSuccess('TimeEntry deleted successfully.');
    }

    /**
     * @return JsonResponse
     */
    public function getUserLastTask()
    {
        $result = $this->timeEntryRepository->myLastTask();

        return $this->sendResponse($result, 'User Task retrieved successfully.');
    }

    /**
     * @param  int  $projectId
     * @param  Request  $request
     * @return JsonResponse
     */
    public function getTasks($projectId, Request $request)
    {
        $taskId = (! is_null($request->get('task_id', null))) ? $request->get('task_id') : null;
        $taskUserId = (! is_null($request->get('user_id', null))) ? $request->get('user_id') : null;
        $result = $this->timeEntryRepository->getTasksByProject($projectId, $taskId, $taskUserId);

        return $this->sendResponse($result, 'Project Tasks retrieved successfully.');
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function getStartTimer(Request $request)
    {
        $this->timeEntryRepository->broadcastStartTimerEvent($request->all());

        return $this->sendSuccess('Start timer broadcasts successfully.');
    }

    /**
     * @return string
     */
    public function copyTodayActivity()
    {
        $timeEntries = $this->timeEntryRepository->getTodayEntries();

        $note = '**'.__('messages.common.today_time_entry_activity').'- '.Carbon::now()->format('jS M Y')."**\n";

        $projects = [];
        /** @var TimeEntry $entry */
        foreach ($timeEntries as $entry) {
            $projects[$entry->task->project->name][$entry->task_id]['name'] = $entry->task->title;
            if (! isset($projects[$entry->task->project->name][$entry->task_id]['note'])) {
                $projects[$entry->task->project->name][$entry->task_id]['note'] = '';
            }
            $projects[$entry->task->project->name][$entry->task_id]['note'] .= "\n".$entry->note."\n";
        }

        foreach ($projects as $name => $project) {
            $note .= "\n".$name."\n";

            foreach ($project as $task) {
                $note .= "\n* ".$task['name'];
                $note .= $task['note'];
            }
        }

        return $note;
    }

    /**
     * @param  TimeEntry  $timeEntry
     * @return mixed
     */
    public function showTimeEntryNote(TimeEntry $timeEntry)
    {
        return $this->sendResponse($timeEntry, 'Retrieved successfully.');
    }

    /**
     * @param $projectId
     * @return JsonResponse
     */
    public function getUsers($projectId)
    {
        /** @var Project $project */
        $project = Project::findOrFail($projectId);

        $result = $this->timeEntryRepository->getUsersByProject($project);

        return $this->sendResponse($result, 'Users retrieved successfully.');
    }

    /**
     * @param  Request  $request
     * @return mixed
     *
     * @throws Exception
     */
    public function taskTimeEntryFilter(Request $request)
    {
        if ($request->ajax()) {
            return Datatables::of((new TimeEntryDataTable())->get($request->only([
                'filter_activity', 'filter_date', 'taskID', 'filter_user', 'filter_project',
            ])))->make(true);
        }
    }
}
