<?php

namespace App\Repositories;

use App\Models\ActivityType;
use App\Models\Comment;
use App\Models\Project;
use App\Models\Tag;
use App\Models\Task;
use App\Models\UserNotification;
use Auth;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * Class TaskRepository.
 */
class TaskRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'title',
        'status',
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
        return Task::class;
    }

    /**
     * @param  int  $id
     * @param  array  $columns
     * @return Task
     */
    public function find($id, $columns = ['*'])
    {
        return $this->findOrFail($id, ['tags', 'project', 'taskAssignee', 'attachments']);
    }

    /**
     * @param  array  $input
     * @return Task|Collection
     *
     * @throws Exception
     */
    public function store($input)
    {
        $uniqueTaskNumber = $this->getUniqueTaskNumber($input['project_id']);
        $input['task_number'] = $uniqueTaskNumber;
        $this->validateTaskData($input);
        $input['status'] = ! empty(getSettings('default_task_status')->value) ? getSettings('default_task_status')->value : '0';
        $input['description'] = is_null($input['description']) ? '' : $input['description'];
        $input['estimate_time'] = is_null($input['estimate_time_hours']) ? $input['estimate_time_days'] : $input['estimate_time_hours'];
        $input['estimate_time_type'] = is_null($input['estimate_time']) ? null : $input['estimate_time_type'];
        try {
            DB::beginTransaction();
            $input['created_by'] = getLoggedInUserId();
            $input['description'] = htmlentities($input['description']);
            $task = Task::create($input);

            if (isset($input['tags']) && ! empty($input['tags'])) {
                $this->attachTags($task, $input['tags']);
            }

            if (isset($input['assignees']) && ! empty($input['assignees'])) {
//                array_push($input['assignees'], getLoggedInUserId());
                $task->taskAssignee()->sync($input['assignees']);
                foreach ($input['assignees'] as $user) {
                    if ($user != getLoggedInUserId()) {
                        UserNotification::create([
                            'title' => 'New Task Assigned',
                            'description' => $task->title.' assigned to you',
                            'type' => Task::class,
                            'user_id' => $user,
                        ]);
                    }
                }
            } else {
                $task->taskAssignee()->sync(getLoggedInUserId());
            }
            if(isset($input['file']) && ! empty($input['file'])){
                foreach ($input['file'] as $file){
                    /** @var Media $attachment */
                    $attachment = $task->addMedia($file)->toMediaCollection(Task::PATH, config('app.media_disc'));
                    $attachment['file_url'] = $attachment->getFullUrl();
                }
            }
            $project = Project::find($input['project_id']);
            activity()
                ->causedBy(getLoggedInUser())
                ->withProperties(['modal' => Task::class, 'data' => 'of '.$project->name])
                ->performedOn($project)
                ->useLog('Task Created')
                ->log('Created new task '.$input['title']);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            throw new BadRequestHttpException($e->getMessage());
        }

        return $task;
    }

    /**
     * @param  array  $input
     * @param  int  $id
     * @return true
     *
     * @throws Exception
     */
    public function update($input, $id)
    {
        $task = $this->findOrFail($id);
        Project::findOrFail($input['project_id']);
        $input['description'] = is_null($input['description']) ? '' : $input['description'];
        if (array_key_exists('estimate_time_type', $input)) {
            $input['estimate_time'] = is_null($input['estimate_time_hours']) ? $input['estimate_time_days'] : $input['estimate_time_hours'];
            $input['estimate_time_type'] = is_null($input['estimate_time']) ? null : $input['estimate_time_type'];
        }
        if ($task->project_id != $input['project_id']) {
            $uniqueTaskNumber = $this->getUniqueTaskNumber($input['project_id']);
            $input['task_number'] = $uniqueTaskNumber;
        }

        try {
            DB::beginTransaction();
            $input['description'] = htmlentities($input['description']);
            $task->update($input);

            $tags = ! empty($input['tags']) ? $input['tags'] : [];
            $this->attachTags($task, $tags);

            if (isset($input['assignees']) && ! empty($input['assignees'])) {
//                array_push($input['assignees'], getLoggedInUserId());
                $task->taskAssignee()->sync($input['assignees']);
                foreach ($input['assignees'] as $user) {
                    if ($user != getLoggedInUserId()) {
                        UserNotification::create([
                            'title' => 'New Task Assigned',
                            'description' => $task->title.' assigned to you',
                            'type' => Task::class,
                            'user_id' => $user,
                        ]);
                    }
                }
            } else {
                $user['task_assignees'] = $task->taskAssignee()->pluck('user_id')->toArray();
//                array_push($user['task_assignees'], getLoggedInUserId());
                $task->taskAssignee()->sync($user['task_assignees']);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            throw new BadRequestHttpException($e->getMessage());
        }

        return $task->fresh();
    }

    /**
     * @param  array  $input
     * @param  Task|null  $task
     * @return bool
     */
    public function validateTaskData($input, $task = null)
    {
        Project::findOrFail($input['project_id']);

        if (! empty($task) && $input['due_date'] == $task->due_date) {
            return true;
        }

        if (Carbon::parse($input['due_date'])->toDateString() < Carbon::now()->toDateString()) {
            throw new BadRequestHttpException('due_date must be greater than today\'s date.');
        }

        return true;
    }

    /**
     * @return array
     */
    public function getTaskData()
    {
        /** @var ProjectRepository $projectRepo */
        $projectRepo = app(ProjectRepository::class);
        $loginUserProjects = $projectRepo->getLoginUserAssignProjectsArr();
        $data['projects'] = $loginUserProjects;

        /** @var TagRepository $tagRepo */
        $tagRepo = app(TagRepository::class);
        $data['tags'] = $tagRepo->getTagList()->toArray();

        /** @var UserRepository $userRepo */
        $userRepo = app(UserRepository::class);
        $data['users'] = $userRepo->getUserList();

        // return all users who has manage task permission otherwise return only logged in user
        if (getLoggedInUser()->hasPermissionTo('manage_all_tasks')) {
            $data['assignees'] = $data['users'];
        } else {
            $data['assignees'] = $data['users']->only(getLoggedInUserId());
        }

        /** @var ActivityTypeRepository $activityTypeRepo */
        $activityTypeRepo = app(ActivityTypeRepository::class);
        $data['activityTypes'] = $activityTypeRepo->getActivityTypeList();

        $statusArr = Task::$statusArr;
        $data['status'] = $statusArr;
        unset($statusArr[Task::$status['STATUS_ALL']]);
        $data['taskStatus'] = $statusArr;
        $data['tasks'] = $this->getTaskList(array_keys($loginUserProjects));
        $data['priority'] = Task::PRIORITY;
        $data['taskBadges'] = $this->getStatusBadge();
        $data['perPageOption'] = Task::PER_PAGE_OPTION;
        $data['tasksFilterOptions'] = Task::TASK_FILTER_OPTION;

        return $data;
    }

    /**
     * @return array
     */
    public function getStatusBadge()
    {
        return [
            Task::$status['STATUS_ACTIVE'] => 'badge-light',
            Task::$status['STATUS_COMPLETED'] => 'badge-success',
        ];
    }

    /**
     * @param  array  $projectIds
     * @return mixed
     */
    public function getTaskList($projectIds = [])
    {
        $query = Task::toBase()->orderBy('title');
        if (! empty($projectIds)) {
            $query = $query->whereIn('project_id', $projectIds);
        }

        return $query->pluck('title', 'id');
    }

    /**
     * @param  int  $id
     * @return bool
     */
    public function updateStatus($id)
    {
        $task = Task::findOrFail($id);
        $status = ($task->status == Task::$status['STATUS_COMPLETED']) ? Task::$status['STATUS_ACTIVE'] : Task::$status['STATUS_COMPLETED'];
        $completedOn = $status == Task::$status['STATUS_COMPLETED'] ? Carbon::now() : null;
        $task->update([
            'status' => $status,
            'completed_on' => $completedOn,
        ]);

        return true;
    }

    /**
     * @param  Task  $task
     * @param  array  $tags
     * @return bool|void
     */
    public function attachTags($task, $tags)
    {
        $newTags = collect($tags)->filter(function ($field) {
            return ! is_numeric($field);
        });

        if (! count($newTags)) {
            $task->tags()->sync($tags);

            return;
        }

        $existingTags = collect($tags)->filter(function ($field) {
            return is_numeric($field);
        });
        $task->tags()->sync($existingTags);

        $tagIds = [];
        foreach ($newTags as $tag) {
            $tagIds[] = Tag::create([
                'name' => $tag,
                'created_by' => getLoggedInUserId(),
            ])->id;
        }
        $task->tags()->attach($tagIds);

        return true;
    }

    /**
     * @param  int  $id
     * @param  array  $input
     * @return Task
     */
    public function getTaskDetails($id, $input = [])
    {
        $task = Task::with([
            'timeEntries' => function (HasMany $query) use ($input) {
                if (isset($input['user_id']) && $input['user_id'] > 0) {
                    $query->where('time_entries.user_id', '=', $input['user_id']);
                }

                if (! empty($input['start_time']) && ! empty($input['end_time'])) {
                    $query->whereBetween('start_time', [$input['start_time'], $input['end_time']]);
                }
                $query->with('user');
            }, 'project',
        ])->findOrFail($id);

        $minutes = $task->timeEntries->pluck('duration')->sum();
        $totalDuration = 0;
        if ($minutes > 1) {
            $totalDuration = sprintf('%02d Hours and %02d Minutes', floor($minutes / 60), $minutes % 60);
        }
        $task->totalDuration = $totalDuration;
        $task->totalDurationMin = $minutes;

        return $task;
    }

    /**
     * @param  array  $input
     * @return array
     */
    public function myTasks($input = [])
    {
        $user = getLoggedInUser();
        /** @var Builder|Task $query */
        $query = Task::whereNotIn('status', [Task::$status['STATUS_COMPLETED']]);
        $query = $query->whereHas('taskAssignee', function (Builder $query) {
            $query->where('user_id', getLoggedInUserId());
        });

        if ($input['project_id'] != 'null') {
            $query->ofProject($input['project_id']);
        }

        $assignedTasks = $query->orderBy('title')->pluck('title', 'id');

        return [
            'activities' => ActivityType::toBase()->orderBy('name')->get(['name', 'id']),
            'tasks' => $assignedTasks,
        ];
    }

    /**
     * @param  int  $projectId
     * @return int|string|null
     */
    public function getUniqueTaskNumber($projectId)
    {
        /** @var Task $task */
        $task = Task::withTrashed()->ofProject($projectId)->where('task_number', '!=', '')->orderByDesc('task_number')
            ->first();

        $uniqueNumber = (empty($task)) ? 1 : $task->task_number + 1;
        $isUnique = false;
        while (! $isUnique) {
            $task = Task::ofProject($projectId)->where('task_number', '=', $uniqueNumber)->first();
            if (empty($task)) {
                $isUnique = true;
            } else {
                $uniqueNumber++;
            }
        }

        return $uniqueNumber;
    }

    /**
     * @param  string  $projectPrefix
     * @param  string  $taskNumber
     * @return Task|void
     */
    public function show($projectPrefix, $taskNumber)
    {
        /** @var Project $project */
        $project = Project::wherePrefix($projectPrefix)->first();
        if (empty($project)) {
            return;
        }

        /** @var Task $task */
        $task = Task::ofProject($project->id)->whereTaskNumber($taskNumber)
            ->with([
                'tags',
                'project',
                'taskAssignee',
                'attachments',
                'comments.createdUser.media',
                'timeEntries.user',
                'timeEntries.activityType',
                'comments' => function (HasMany $query) {
                    $query->orderByDesc('created_at');
                },
            ])->first();

        if (! empty($task)) {
            return $task;
        }
    }

    /**
     * @param $task
     * @param $files
     * @return Media
     */
    public function uploadFile($task, $files)
    {
        try {
            /** @var Media $attachment */
            $attachment = $task->addMedia($files)->toMediaCollection(Task::PATH, config('app.media_disc'));
            $attachment['file_url'] = $attachment->getFullUrl();
        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
        $attachment['userId'] = $task->created_by;
        
        return $attachment;
    }

    /**
     * @param  int  $id
     * @return array
     */
    public function getAttachments($id)
    {
        /** @var Task $task */
        $task = $this->find($id);
        $attachments = $task->media;

        $result = [];

        foreach ($attachments as $attachment) {
            $obj['id'] = $attachment->id;
            $obj['name'] = $attachment->file_name;
            $obj['size'] = $attachment->size;
            $obj['url'] = $attachment->getFullUrl();
            $result[] = $obj;
        }

        return $result;
    }

    /**
     * @param  array  $input
     * @return Builder|Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function addComment($input)
    {
        $input['created_by'] = Auth::id();
        $comment = Comment::create($input);

        return Comment::with('createdUser')->findOrFail($comment->id);
    }

    /**
     * @param  int  $id
     * @param  array  $input
     * @return bool
     */
    public function updateTaskStatus($id, $input)
    {
        $task = Task::findOrFail($id);
        
        $updateData = ['status' => $input['status']];
        
        // If task is being marked as completed, set the completed_on date
        if ($input['status'] == Task::STATUS_COMPLETED) {
            $updateData['completed_on'] = now();
        }
        // If task is being changed from completed to another status, clear the completed_on date
        elseif ($task->status == Task::STATUS_COMPLETED && $input['status'] != Task::STATUS_COMPLETED) {
            $updateData['completed_on'] = null;
        }
        
        $task->update($updateData);
    
        return true;
    }

    /**
     * @param  int  $id
     * @param  array  $input
     * @return bool
     */
    public function updateKanbanTask($input, $id)
    {
        $task = Task::findOrFail($id);

        $input['description'] = is_null($input['description']) ? '' : $input['description'];

        $task->update($input);

        return true;
    }

    /**
     * @return mixed
     */
    public function getLoginUserProjects()
    {
        /** @var ProjectRepository $projectRepo */
        $projectRepo = app(ProjectRepository::class);
        $loginUserProjects = $projectRepo->getLoginUserAssignProjectsArr();
        $data['projects'] = $loginUserProjects;

        /** @var UserRepository $userRepo */
        $userRepo = app(UserRepository::class);
        $data['users'] = $userRepo->getUserList();

        return $data;
    }

    /**
     * @param  int  $id
     * @return Builder|Builder[]|\Illuminate\Database\Eloquent\Collection|Model|null
     */
    public function getKanbanTaskDetails($id)
    {
        $task = Task::with([
            'project', 'taskAssignee', 'comments', 'createdUser',
        ])->findOrFail($id);

        return $task;
    }

    /**
     * @param  array  $input
     * @param  int  $id
     * @return mixed
     */
    public function updateKanbanTaskDetails($input, $id)
    {
        $task = Task::findOrFail($id);
        if (array_key_exists('description', $input)) {
            $input['description'] = is_null($input['description']) ? '' : $input['description'];
        }

        if (! empty($input['user_id'])) {
            $task->taskAssignee()->attach($input['user_id']);
        }

        $task->update($input);

        $task = Task::with('taskAssignee')->findOrFail($id);

        return $task;
    }

    /**
     * @param  array  $task
     * @param  array  $input
     * @return array
     */
    public function uploadAttachments($task, $input)
    {
        $data = [];
//        $data['total_files'] = $input['TotalFiles'];
        $tasks = Task::with('createdUser')->find($task->id);
        $data['user'] = $tasks->createdUser->name;
        $data['userId'] = $tasks->createdUser->id;
        try {
            if (! empty($input['file'])) {
//                foreach ($input['files'] as $file)
//                {
                $data['attachment'] = $task->addMedia($input['file'])->toMediaCollection(Task::PATH,
                    config('app.media_disc'));
                $data['file_url'] = $data['attachment']->getFullUrl();
//                    array_push($data,$data);
//                }
            }
        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }

        return $data;
    }
}
