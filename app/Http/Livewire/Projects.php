<?php

namespace App\Http\Livewire;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Livewire\WithPagination;
use Str;

class Projects extends SearchableComponent
{
    use WithPagination;

    public $client = null;

    public $clientFilter = '';

    public $projectStatus = '1';

    public $userId = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $projects = $this->searchProjects($this->client);
        $totalProjects = Project::count();

        return view('livewire.projects', [
            'projects' => $projects,
            'totalProjects' => $totalProjects,
        ])->with('search');
    }

    /**
     * @param $client
     * @return LengthAwarePaginator
     */
    public function searchProjects($client)
    {
        $this->setQuery($this->getQuery()->with([
            'client', 'tasks', 'users.media', 'createdUser', 'openTasks',
        ])->orderBy('name', 'asc')->withCount([
            'tasks' => function ($query) {
                $query->where('status', '=', Task::$status['STATUS_ACTIVE']);
            }, 'users',
        ]));

        $this->getQuery()->when(! empty($this->userId), function (Builder $q) {
            $this->getQuery()->whereHas('users', function (Builder $q) {
                $q->where('user_id', '=', $this->userId);
            });
        });
        $this->getQuery()->where(function (Builder $query) {
            if (empty($this->clientFilter)) {
                $this->filterResults();
            }
        });

        $this->getQuery()->when(! empty($this->clientFilter), function (Builder $q) {
            if (! empty($this->search)) {
                $searchString = '%'.$this->search.'%';
                $q->orWhereRaw('lower(name) like ?', $searchString);
            }
            $q->WhereHas('client', function (Builder $q) {
                $q->where('id', $this->clientFilter);
            });
        });

        $this->getQuery()->when(! empty($client), function (Builder $q) use ($client) {
            $q->WhereHas('client', function (Builder $q) use ($client) {
                $q->where('name', 'like', '%'.$client.'%');
            });
        });

        $this->getQuery()->when(! empty($this->projectStatus), function (Builder $q) {
            $q->where('status', $this->projectStatus);
        });

        return $this->paginate($withoutSearching = false);
    }

    protected $listeners = [
        'refresh' => '$refresh',
        'filterProjects',
        'projectsStatus',
        'usersProject',
        'updateAssigneesProject',
    ];

    /**
     * @param $status
     */
    public function projectsStatus($status)
    {
        $this->projectStatus = $status;
    }

    public function usersProject($id)
    {
        $this->userId = $id;
    }

    /**
     * @param $clientId
     */
    public function filterProjects($clientId)
    {
        $this->clientFilter = $clientId;
        $this->resetPage();
    }

    public function model()
    {
        return Project::class;
    }

    public function searchableFields()
    {
        return [
            'name',
            'prefix',
            'client.name',
        ];
    }

    /**
     * @param $input
     * @param $id
     */
    public function updateAssigneesProject($input, $id)
    {
        $project = Project::with('users')->findOrFail($id);
        $assignees = ! empty($input) ? $input : $input = getLoggedInUserId();
        $oldUserIds = $project->users->pluck('id')->toArray();
        $project->users()->sync($assignees);

        if (is_array($input)) {
            $userIds = array_diff($assignees, $oldUserIds);
            $removedUserIds = array_diff($oldUserIds, $assignees);
            $users = User::whereIn('id', $userIds)->get();
            if ($users->count() > 0) {
                $u = [];
                foreach ($users as $user) {
                    array_push($u, $user->name);
                    UserNotification::create([
                        'title' => 'New Project Assigned',
                        'description' => $project->name.' assigned to you',
                        'type' => Project::class,
                        'user_id' => $user->id,
                    ]);
                }
                activity()
                    ->causedBy(getLoggedInUser())
                    ->withProperties(['modal' => Project::class, 'data' => ''])
                    ->performedOn($project)
                    ->useLog('Project Assignee Updated')
                    ->log('Assigned '.$project->name.' to '.implode(',', $u));
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
        }
    }

    public function filterResults()
    {
        $searchableFields = $this->searchableFields();
        $search = $this->search;

        $this->getQuery()->when(! empty($search), function (Builder $q) use ($search, $searchableFields) {
            $this->getQuery()->where(function (Builder $q) use ($search, $searchableFields) {
                $searchString = '%'.$search.'%';
                foreach ($searchableFields as $field) {
                    if (Str::contains($field, '.')) {
                        $field = explode('.', $field);
                        $q->orWhereHas($field[0], function (Builder $query) use ($field, $searchString) {
                            $query->whereRaw("lower($field[1]) like ?", $searchString);
                        });
                    } else {
                        $q->orWhereRaw("lower($field) like ?", $searchString);
                    }
                }
            });
        });

        return $this->getQuery();
    }
}
