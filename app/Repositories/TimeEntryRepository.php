<?php

namespace App\Repositories;

use App\Events\StartTimer;
use App\Events\StopWatchStop;
use App\Models\Task;
use App\Models\TimeEntry;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as BuilderAlias;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Log;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class TimeEntryRepository.
 *
 * @version May 3, 2019, 9:46 am UTC
 */
class TimeEntryRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'task_id',
        'activity_type_id',
        'user_id',
        'start_time',
        'end_time',
        'duration',
        'entry_type',
    ];

    /**
     * Return searchable fields.
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model.
     **/
    public function model()
    {
        return TimeEntry::class;
    }

    /**
     * @return array
     */
    public function getEntryData()
    {
        /** @var ProjectRepository $projectRepo */
        $projectRepo = app(ProjectRepository::class);
        $data['projects'] = $projectRepo->getLoginUserAssignTasksProjects();
        $data['projectsForFilter'] = $projectRepo->getProjectsHavingPermission();

        /** @var UserRepository $userRepo */
        $userRepo = app(UserRepository::class);
        $data['users'] = $userRepo->getUserList();

        /** @var ActivityTypeRepository $activityTypeRepo */
        $activityTypeRepo = app(ActivityTypeRepository::class);
        $data['activityTypes'] = $activityTypeRepo->getActivityTypeList();

        $data['tasks'] = Task::whereHas('taskAssignee', function (Builder $query) {
            $query->where('user_id', getLoggedInUserId());
        })->orderBy('title')->pluck('title', 'id');

        return $data;
    }

    /**
     * @return array|null|void
     */
    public function myLastTask()
    {
        /** @var TimeEntry $timeEntry */
        $timeEntry = TimeEntry::ofCurrentUser()->latest()->first();
        if (empty($timeEntry)) {
            return;
        }

        return [
            'task_id' => $timeEntry->task_id,
            'activity_id' => $timeEntry->activity_type_id,
            'project_id' => $timeEntry->task->project_id,
        ];
    }

    /**
     * @param  int  $projectId
     * @param  int|null  $taskId
     * @return Collection
     */
    public function getTasksByProject($projectId, $taskId = null, $taskUserId = null)
    {
        $user = getLoggedInUser();
        /** @var Builder $query */
        $query = Task::ofProject($projectId)
            ->where('status', '=', Task::$status['STATUS_ACTIVE']);
        if (! $user->can('manage_projects') && empty($taskUserId)) {
            $query = $query->whereHas('taskAssignee', function (Builder $query) {
                $query->where('user_id', getLoggedInUserId());
            });
        }

        if (! empty($taskUserId)) {
            $query = $query->whereHas('taskAssignee', function (Builder $query) use ($taskUserId) {
                $query->where('user_id', $taskUserId);
            });
        }

        if (! empty($taskId)) {
            $query->orWhere('id', $taskId);
        }

        $result = $query->pluck('title', 'id');

        return $result;
    }

    /**
     * @param  int  $id
     * @return mixed
     */
    public function getTimeEntryDetail($id)
    {
        $result = TimeEntry::leftJoin('tasks as t', 't.id', '=', 'time_entries.task_id')
            ->where('time_entries.id', '=', $id)
            ->select('time_entries.*', 't.project_id')
            ->first();

        return $result;
    }

    /**
     * @param  array  $input
     * @return bool
     */
    public function store($input)
    {
        $input = $this->validateInput($input);

        $this->assignTaskToAdmin($input);

        if ($input['duration'] > 0) {
            $timeEntry = TimeEntry::create($input);
        }

        return true;
    }

    /**
     * @param  array  $input
     * @param  int  $id
     * @return bool
     */
    public function updateTimeEntry($input, $id)
    {
        $input = $this->validateInput($input, $id);

        /** @var TimeEntry $timeEntry */
        $timeEntry = TimeEntry::findOrFail($id);

        $existEntry = $timeEntry->only([
            'id',
            'task_id',
            'activity_type_id',
            'user_id',
            'start_time',
            'end_time',
            'duration',
            'note',
        ]);

        $inputDiff = array_diff($existEntry, $input);
        if (! empty($inputDiff)) {
            Log::info('Entry Id: '.$timeEntry->id);
            Log::info('Task Id: '.$timeEntry->task_id);
            Log::info('fields changed: ', $inputDiff);
            Log::info('Entry updated by: '.Auth::user()->name);
        }

        $timeEntryType = ($timeEntry->entry_type == TimeEntry::STOPWATCH) ?
            $this->checkTimeUpdated($timeEntry, $input) :
            $timeEntry->entry_type;
        $input['entry_type'] = $timeEntryType;
        if (! empty($input['duration']) && empty($input['start_time']) || empty($input['end_time'])) {
            if ($timeEntry->duration != $input['duration']) {
                $input['start_time'] = '';
                $input['end_time'] = '';
            }
        }
        if ($input['duration'] > 0) {
            $this->update($input, $id);
        }

        return true;
    }

    /**
     * @param  array  $input
     * @param  null  $id
     * @return array|JsonResponse
     */
    public function validateInput($input, $id = null)
    {
        $startTime = Carbon::parse($input['start_time']);
        $endTime = Carbon::parse($input['end_time']);
        $input['duration'] = $endTime->diffInMinutes($startTime);
        if ($startTime > $endTime) {
            throw new BadRequestHttpException('Invalid start time and end time.');
        }

        $now = Carbon::now()->format('Y-m-d');
        if ($startTime->format('Y-m-d') > $now) {
            throw new BadRequestHttpException('Start time must be less than or equal to current time.');
        }

        if ($endTime->format('Y-m-d') > $now) {
            throw new BadRequestHttpException('End time must be less than or equal to current time.');
        }

        if ($input['duration'] > 720) {
            throw new BadRequestHttpException('Time Entry must be less than 12 hours.');
        }

        $loggedInUser = getLoggedInUser();

        if (! $loggedInUser->can('manage_time_entries') || ! isset($input['user_id'])) {
            $input['user_id'] = getLoggedInUserId();
        }
        $this->checkDuplicateEntry($input, $id);

        if (! isset($input['note']) || empty($input['note'])) {
            $input['note'] = 'N/A';
        }

        return $input;
    }

    /**
     * @param $timeEntry
     * @param $input
     * @return int
     */
    public function checkTimeUpdated($timeEntry, $input)
    {
        if ($input['start_time'] != $timeEntry->start_time || $input['end_time'] != $timeEntry->end_time) {
            return TimeEntry::VIA_FORM;
        }

        return TimeEntry::STOPWATCH;
    }

    /**
     * @param  array  $input
     * @param  int|null  $id
     * @return bool
     */
    public function checkDuplicateEntry($input, $id = null)
    {
        $timeArr = [$input['start_time'], $input['end_time']];
        $userId = $input['user_id'] ?? getLoggedInUserId();
        $query = TimeEntry::whereUserId($userId)
            ->where(function (Builder $q) use ($timeArr) {
                $q->whereBetween('start_time', $timeArr)
                    ->orWhereBetween('end_time', $timeArr)
                    ->orWhereRaw("('$timeArr[0]' between start_time and end_time or '$timeArr[1]' between start_time and end_time)");
            });

        if (! empty($id) && $id > 0) {
            $query->where('id', '!=', $id);
        }

        $timeEntry = $query->first();
        if (! empty($timeEntry)) {
            throw new BadRequestHttpException('Time entry between this duration already exist.');
        }

        return true;
    }

    /**
     * Start timer broadcast event.
     *
     * @param  array  $input
     */
    public function broadcastStartTimerEvent($input)
    {
        broadcast(new StartTimer($input))->toOthers();
    }

    /**
     * Stop timer broadcast event.
     */
    public function broadcastStopTimerEvent()
    {
        broadcast(new StopWatchStop())->toOthers();
    }

    /**
     * @param  array  $input
     * @return bool
     */
    public function assignTaskToAdmin($input)
    {
        $task = Task::find($input['task_id']);
        $taskAssignees = $task->taskAssignee->pluck('id')->toArray();

        $task->taskAssignee()->sync($taskAssignees);

        return true;
    }

    /**
     * @return TimeEntry[]|Builder[]|BuilderAlias[]|Collection
     */
    public function getTodayEntries()
    {
        return TimeEntry::with('task.project')
            ->whereDate('start_time', '=', Carbon::now()->format('Y-m-d'))
            ->where('user_id', '=', Auth::id())
            ->get();
    }

    /**
     * @param $project
     * @return mixed
     */
    public function getUsersByProject($project)
    {
        $user = getLoggedInUser();
        /** @var Builder $query */
        $userIds = $project->users()->pluck('user_id')->toArray();
        $users = User::whereIn('id', $userIds)->where('is_active', 1)->pluck('name', 'id');

        return $users;
    }

    /**
     * @return mixed
     */
    public function getTimeEntryData()
    {
        /** @var ProjectRepository $projectRepo */
        $projectRepo = app(ProjectRepository::class);
        $loginUserProjects = $projectRepo->getLoginUserAssignProjectsArr();
        $data['projects'] = $loginUserProjects;

        /** @var ActivityTypeRepository $activityTypeRepo */
        $activityTypeRepo = app(ActivityTypeRepository::class);
        $data['activityTypes'] = $activityTypeRepo->getActivityTypeList();

        /** @var UserRepository $userRepo */
        $userRepo = app(UserRepository::class);
        $data['users'] = $userRepo->getUserList();

        /** @var TaskRepository $taskRepo */
        $taskRepo = app(TaskRepository::class);
        $data['tasks'] = $taskRepo->getTaskList(array_keys($loginUserProjects));

        return $data;
    }

    /**
     * @param  null  $input
     * @return array
     */
    public function getTimeEntriesForCalenderView($input = null)
    {
        $query = TimeEntry::with(['user', 'task.project']);
        $userRole = getLoggedInUser()->hasRole('Admin');
        if (! $userRole) {
            $query->whereUserId(getLoggedInUserId());
        }
        $query->when(isset($input['userId']) && ! empty($input['userId']),
            function (Builder $q) use ($input) {
                $q->where('user_id', $input['userId']);
            }
        );

        if (isset($input['start_date']) && isset($input['end_date'])) {
            $query->whereDate('start_time', '>=', $input['start_date'])->whereDate('end_time', '<=',
                $input['end_date']);
        } else {
            $query->where('start_time', '>=', Carbon::now()->startOfWeek())->where('end_time', '<=',
                Carbon::now()->endOfWeek());
        }

        $timeEntries = $query->get();
        $result = [];
        foreach ($timeEntries as $timeEntry) {
            $data['totalDuration'] = 0;
            $data['id'] = $timeEntry->id;
            $data['title'] = $timeEntry->task->project->prefix.' - '.$timeEntry->task->title;
            $data['user'] = $timeEntry->user->name;
            $data['color'] = $timeEntry->task->project->color;
            $data['totalDuration'] += $timeEntry->duration;
            $data['start'] = Carbon::parse($timeEntry['start_time'])->toDateTimeString();
            $data['end'] = Carbon::parse($timeEntry['end_time'])->toDateTimeString();
            $data['time_duration'] = $timeEntry->duration;
            $result[] = $data;
        }

        return array_values($result);
    }
}
