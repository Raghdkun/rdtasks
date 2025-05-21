<?php

namespace App\Repositories;

use App\Mail\ProjectAssignToUser;
use App\Models\Project;
use App\Models\ProjectActivity;
use App\Models\Task;
use App\Models\TimeEntry;
use App\Models\User;
use App\Models\UserNotification;
use Auth;
use Exception;
use Illuminate\Container\Container as Application;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * Class ProjectRepository.
 */
class ProjectRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'team',
        'description',
        'client_id',
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
        return Project::class;
    }

    /**
     * @param  array  $input
     * @return Project
     */
    public function store($input)
    {
        $input['created_by'] = getLoggedInUserId();
        $input['description'] = '';

        $projectInput = Arr::only($input, (new Project())->getFillable());

        $userIds = $input['user_ids'];
        $users = User::whereIn('id', $userIds)->get();
        $this->sendMailToUsers($users);

        $project = Project::create($projectInput);
        $project->users()->sync($input['user_ids']);
        if (! empty($input['user_ids'])) {
            $u = [];
            foreach ($users as $user) {
                array_push($u, $user->name);
                UserNotification::create([
                    'title' => 'New Project Assigned',
                    'description' => 'You are assigned to '.$project->name,
                    'type' => Project::class,
                    'user_id' => $user->id,
                ]);
            }
            activity()
                ->causedBy(getLoggedInUser())
                ->withProperties(['modal' => Project::class, 'data' => ''])
                ->performedOn($project)
                ->useLog('Project Assign To User')
                ->log('Assigned '.$project->name.' to '.implode(',', $u));
        }
        activity()
            ->causedBy(getLoggedInUser())
            ->withProperties(['modal' => Project::class, 'data' => ' '.$project->name])
            ->performedOn($project)
            ->useLog('Project Created')
            ->log('Created project');

        return $project->fresh();
    }

    /**
     * @param  array  $input
     * @param  int  $id
     * @return Project
     */
    public function update($input, $id)
    {
        $input['description'] = is_null($input['description']) ? '' : $input['description'];
        $project = Project::findOrFail($id);

        $oldUserIds = $project->users->pluck('id')->toArray();
        $oldStatus = $project->status;
        $newUserIds = $input['user_ids'];
        $removedUserIds = array_diff($oldUserIds, $newUserIds);
        $userIds = array_diff($newUserIds, $oldUserIds);
        $users = User::whereIn('id', $userIds)->get();
        $this->sendMailToUsers($users);

        $project->update($input);
        $project->users()->sync($input['user_ids']);
        if ($oldStatus != $input['status']) {
            foreach ($newUserIds as $userId) {
                UserNotification::create([
                    'title' => 'Project Status Changed',
                    'description' => 'Project Status changed from '.Project::STATUS[$oldStatus].' to '.Project::STATUS[$input['status']],
                    'type' => Project::class,
                    'user_id' => $userId,
                ]);
            }
            if (! empty($project->client->user_id)) {
                UserNotification::create([
                    'title' => 'Project Status Changed',
                    'description' => 'Project Status changed from '.Project::STATUS[$oldStatus].' to '.Project::STATUS[$input['status']],
                    'type' => Project::class,
                    'user_id' => $project->client->user_id,
                ]);
            }
        }
        if (! empty($removedUserIds)) {
            foreach ($removedUserIds as $removedUser) {
                UserNotification::create([
                    'title' => 'Removed From Project',
                    'description' => 'You removed from '.$project->name,
                    'type' => Project::class,
                    'user_id' => $removedUser,
                ]);
            }
        }
        if ($users->count() > 0) {
            $u = [];
            foreach ($users as $user) {
                array_push($u, $user->name);
                UserNotification::create([
                    'title' => 'New Project Assigned',
                    'description' => 'You are assigned to '.$project->name,
                    'type' => Project::class,
                    'user_id' => $user->id,
                ]);
                foreach ($oldUserIds as $oldUser) {
                    UserNotification::create([
                        'title' => 'New User Assigned to Project',
                        'description' => $user->name.' assigned to '.$project->name,
                        'type' => Project::class,
                        'user_id' => $oldUser,
                    ]);
                }
            }
            activity()
                ->causedBy(getLoggedInUser())
                ->withProperties(['modal' => Project::class, 'data' => ''])
                ->performedOn($project)
                ->useLog('Project Assignee Updated')
                ->log('Assigned '.$project->name.' to '.implode(',', $u));
        }
        activity()
            ->causedBy(getLoggedInUser())
            ->withProperties(['modal' => Project::class, 'data' => ' '.$project->name])
            ->performedOn($project)
            ->useLog('Project Updated')
            ->log('Updated Project');

        return $project->fresh();
    }

    /***
     * @return array
     */
    public function getLoginUserAssignProjectsArr()
    {
        $loggedInUser = getLoggedInUser();

        if ($loggedInUser->can('manage_projects')) {
            return $this->getProjectsList()->toArray();
        }

        return Auth::user()->projects()->where('status', '!=',
            Project::STATUS_FINISHED)->orderBy('name')->get()->pluck('name', 'id')->toArray();
    }

    /**
     * @return Collection
     */
    public function getLoginUserAssignTasksProjects()
    {
        /** @var Builder|Project $query */
        $query = Project::orderBy('name')
            ->whereHas('users', function (Builder $query) {
                $query->where('user_id', getLoggedInUserId());
            })
            ->whereHas('tasks', function (Builder $query) {
                $query->where('status', '=', 0)
                    ->whereHas('taskAssignee', function (Builder $query) {
                        $query->where('user_id', getLoggedInUserId());
                    });
            });

        return $query->pluck('name', 'id');
    }

    /**
     * get clients.
     *
     * @param  int|null  $clientId
     * @return Collection
     */
    public function getProjectsList($clientId = null)
    {
        /** @var Builder|Project $query */
        $query = Project::toBase()->where('status', '!=', Project::STATUS_FINISHED)->orderBy('name');
        if (! is_null($clientId)) {
            $query = $query->whereClientId($clientId);
        }
        if (! getLoggedInUser()->hasPermissionTo('manage_all_tasks')) {
            $query = getLoggedInUser()->projects->toBase(); // get assigned projects list for particular user
        }

        return $query->pluck('name', 'id');
    }

    /**
     * @return Project[]
     */
    public function getMyProjects()
    {
        $query = Project::whereHas('users', function (Builder $query) {
            $query->where('user_id', getLoggedInUserId());
        })
        ->whereHas('tasks', function (Builder $query) {
            $query->where('status', '=', 0)
                ->whereHas('taskAssignee', function (Builder $query) {
                    $query->where('user_id', getLoggedInUserId());
                });
        });

        /** @var Project[] $projects */
        $projects = $query->orderBy('name')->get();

        return $projects;
    }

    /**
     * @param  int  $id
     * @return bool|mixed|void|null
     *
     * @throws Exception
     */
    public function delete($id)
    {
        /** @var Project $project */
        $project = $this->find($id);

        $project->users()->detach();
        $taskIds = Task::whereProjectId($project->id)->pluck('id')->toArray();
        TimeEntry::whereIn('task_id', $taskIds)->update(['deleted_by' => getLoggedInUserId()]);
        TimeEntry::whereIn('task_id', $taskIds)->delete();

        $taskIds = $project->tasks()->pluck('id');
        foreach ($taskIds as $taskId) {
            $task = Task::find($taskId);
            $task->update(['deleted_by' => getLoggedInUserId()]);
            $task->delete();
            $task->tags()->detach();
        }

        $project->expenses()->update(['deleted_by' => getLoggedInUserId()]);
        $project->expenses()->delete();

        $project->update(['deleted_by' => getLoggedInUserId()]);
        $project->delete();

        //invoice delete
        $invoiceProject = DB::table('invoice_projects')->where('project_id', $id)->first();
        if ($invoiceProject) {
            deleteInvoice($invoiceProject->invoice_id);
        }
    }

    /**
     * @return Collection
     */
    public function getProjectsHavingPermission()
    {
        /** @var Builder|Project $query */
        $query = Project::orderBy('name');
        if (! getLoggedInUser()->hasPermissionTo('manage_time_entries')) {
            $query = getLoggedInUser()->projects; // get assigned projects list for particular user
        }

        return $query->pluck('name', 'id');
    }

    /*
     * @param $userId
     *
     * @return Collection
     */
    public function getProjectsByUserId($userId)
    {
        /** @var Builder|Project $query */
        $query = Project::orderBy('name')
            ->whereHas('users', function (Builder $query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->whereHas('tasks', function (Builder $query) use ($userId) {
                $query->where('status', '=', 0)
                    ->whereHas('taskAssignee', function (Builder $query) use ($userId) {
                        $query->where('user_id', $userId);
                    });
            });

        return $query->pluck('name', 'id');
    }

    /**
     * get clients.
     *
     * @param  array|null  $clientId
     * @return Collection
     */
    public function getProjectsByClients($clientId = null)
    {
        /** @var Builder|Project $query */
        $query = Project::orderBy('name');
        if (is_array($clientId) && count($clientId) > 0) {
            $query = $query->whereIn('client_id', $clientId);
        }
        if (! getLoggedInUser()->hasPermissionTo('manage_all_tasks')) {
            $query = getLoggedInUser()->projects; // get assigned projects list for particular user
        }

        return $query->pluck('name', 'id');
    }

    /**
     * @param $project
     * @return mixed
     */
    public function getAssignProjectDetail($project)
    {
        $data['openTasks'] = $project->openTasks->count();
        $data['tasks'] = Task::with('timeEntries', 'taskAssignee.media', 'project')->whereHas('taskAssignee',
            function (Builder $query) {
                $query->where('user_id', getLoggedInUserId());
            })->whereProjectId($project->id)->where('status', '!=', Task::STATUS_COMPLETED)->orderBy('created_at',
            'DESC')->get();
        $data['activities'] = ProjectActivity::with('createdBy')->where('subject_id', '=',
            $project->id)->orderByDesc('created_at')->get();

        return $data;
    }

    /**
     * @param  array  $users
     */
    public function sendMailToUsers($users)
    {
        foreach ($users as $user) {
            $data['userName'] = $user->name;
            $data['projectUrl'] = URL::to('user-assign-projects');
            if (! empty($user->email)) {
                Mail::to($user->email)
                    ->send(new ProjectAssignToUser('emails.assign_project_to_user',
                        'Assign New Projects',
                        $data));
            }
        }
    }

    /**
     * @param $project
     * @return mixed
     */
    public function getProjectsDetails($project)
    {
        $data['tasks'] = Task::with('timeEntries', 'createdUser.media', 'project')->whereProjectId($project->id)
            ->where('status', '!=', Task::STATUS_COMPLETED)->orderBy('created_at', 'DESC')
            ->whereHas('createdUser', function (Builder $q) {
                $q->where('is_active', '=', true);
            })->get();

        return $data;
    }

    public function uploadFile($project, $files)
    {
        try {
            /** @var Media $attachment */
            $attachment = $project->addMedia($files)->toMediaCollection(Project::PATH, config('app.media_disc'));
            $attachment['file_url'] = $attachment->getFullUrl();
        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
        $attachment['userId'] = $project->created_by;

        return $attachment;

    }

    public function getAttachments($id)
    {

        /** @var Task $task */
        $project = $this->find($id);
        $attachments = $project->media;

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
}
