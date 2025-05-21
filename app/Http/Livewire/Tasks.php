<?php

namespace App\Http\Livewire;

use App\Models\Task;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Str;

class Tasks extends SearchableComponent
{
    public $dueDateFilter = '';

    public $userFilter = '';

    public $projectFilter = '';

    public $statusFilter = 0;

    public $taskName = null;

    public $project = null;

    public $projects = [];

    public $tags = [];

    public $perPage = 0;

    public $paginate = 10;

    public $loginUserProjects = [];

    public $orderByFilter = self::CREATED_AT_DESC;

    const CREATED_AT_ASC = 1;

    const CREATED_AT_DESC = 2;

    const COMPLETED_ON_ASC = 3;

    const COMPLETED_ON_DESC = 4;

    const DUE_DATE_ASC = 5;

    const DUE_DATE_DESC = 6;

    public $tasksFilter = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function mount($userId, $projects, $tags)
    {
        $this->userFilter = $userId;
        $this->projects = $projects;
        $this->tags = $tags;
    }

    public function render()
    {
        $this->loginUserProjects = $this->projects;
        $tasks = $this->searchTasks($this->project);
        $data['priority'] = Task::PRIORITY;
        if (! empty($this->userFilter)) {
            $totalTasks = Task::whereHas('taskAssignee', function (Builder $q) {
                $q->where('user_id', $this->userFilter)->where('is_active', '=', true);
            })->count();
        } else {
            $totalTasks = Task::count();
        }
        $data['projects'] = $this->loginUserProjects;
        $data['tags'] = $this->tags;
        $data['search'] = $this->project;

        return view('livewire.tasks', $data, compact('tasks', 'totalTasks'));
    }

    /**
     * @param $project
     * @return LengthAwarePaginator
     */
    public function searchTasks($project)
    {
        $query = $this->getQuery()->with([
            'project', 'timeEntries', 'taskAssignee.media', 'tags',
        ]);
        $this->setQuery($query);

        $this->getQuery()->where(function (Builder $query) {
            $this->filterResults();
        });

        $this->getQuery()->whereIn('project_id', array_keys($this->loginUserProjects));

        $this->getQuery()->when(! empty($project), function (Builder $q) use ($project) {
            $q->WhereHas('project', function (Builder $q) use ($project) {
                $q->where('name', 'like', '%'.$project.'%');
            });
        });

        $this->getQuery()->when($this->statusFilter !== '', function (Builder $q) {
            $q->where('status', $this->statusFilter);
        });

        $this->getQuery()->when(! empty($this->projectFilter), function (Builder $q) {
            $q->ofProject($this->projectFilter);
        });

        $this->getQuery()->when(! empty($this->userFilter), function (Builder $q) {
            $q->whereHas('taskAssignee', function (Builder $q) {
                $q->where('user_id', $this->userFilter)->where('is_active', '=', true);
            });
        });

        $this->getQuery()->when(! empty($this->dueDateFilter), function (Builder $q) {
            $q->where('due_date', $this->dueDateFilter);
        });

        $this->getQuery()->when(! empty($this->orderByFilter) && $this->orderByFilter == self::CREATED_AT_ASC,
            function (Builder $q) {
                $q->orderBy('created_at', 'ASC');
            });

        $this->getQuery()->when(! empty($this->orderByFilter) && $this->orderByFilter == self::CREATED_AT_DESC,
            function (Builder $q) {
                $q->orderBy('created_at', 'DESC');
            });

        $this->getQuery()->when(! empty($this->orderByFilter) && $this->orderByFilter == self::DUE_DATE_ASC,
            function (Builder $q) {
                $q->orderByRaw('-due_date DESC');
            });

        $this->getQuery()->when(! empty($this->orderByFilter) && $this->orderByFilter == self::DUE_DATE_DESC,
            function (Builder $q) {
                $q->orderBy('due_date', 'DESC');
            });

        $this->getQuery()->when(! empty($this->orderByFilter) && $this->orderByFilter == self::COMPLETED_ON_ASC,
            function (Builder $q) {
                $q->orderBy('completed_on', 'ASC');
            });

        $this->getQuery()->when(! empty($this->orderByFilter) && $this->orderByFilter == self::COMPLETED_ON_DESC,
            function (Builder $q) {
                $q->orderBy('completed_on', 'DESC');
            });

        return $this->paginate($withoutSearching = false);
    }

    /**
     * @return mixed|string
     */
    public function model()
    {
        return Task::class;
    }

    /**
     * @return mixed|string[]
     */
    public function searchableFields()
    {
        return [
            'title',
            'priority',
            'project.name',
        ];
    }

    protected $listeners = [
        'refresh' => '$refresh',
        'updateTask',
        'filterTasksByStatus',
        'updateAssignees',
        'filterTasksByProject',
        'filterTasksByUser',
        'filterTasksByDueDate',
        'updateTaskDueDate',
        'filterPerPage',
        'orderByFilter',
    ];

    /**
     * @param  string  $taskName
     * @param  int  $id
     */
    public function updateTask($taskName, $id)
    {
        $this->taskName = $taskName;
        $task = Task::find($id);
        $task->update([
            'title' => $this->taskName,
        ]);
    }

    /**
     * @param  int  $id
     */
    public function filterTasksByStatus($id)
    {
        $this->statusFilter = $id;
    }

    /**
     * @param  array  $input
     * @param  int  $id
     */
    public function updateAssignees($input, $id)
    {
        $task = Task::findOrFail($id);
        $assignees = ! empty($input) ? $input : $input = getLoggedInUserId();
        $oldUserIds = $task->taskAssignee()->pluck('user_id')->toArray();
        if (is_array($input)) {
            $userIds = array_diff($assignees, $oldUserIds);
            $users = User::whereIn('id', $userIds)->get();
            if ($users->count() > 0) {
                foreach ($users as $user) {
                    UserNotification::create([
                        'title' => 'New Task Assigned',
                        'description' => $task->title.' assigned to you',
                        'type' => Task::class,
                        'user_id' => $user->id,
                    ]);
                }
            }
        }
        $task->taskAssignee()->sync($assignees);
    }

    /**
     * @param  int  $id
     */
    public function filterTasksByProject($id)
    {
        $this->projectFilter = $id;
    }

    /**
     * @param  int  $id
     */
    public function filterTasksByUser($id)
    {
        $this->userFilter = $id;
    }

    /**
     * @param  array  $date
     */
    public function filterTasksByDueDate($date)
    {
        $this->dueDateFilter = $date;
    }

    /**
     * @param  int  $page
     */
    public function filterPerPage($page)
    {
        if (empty($page)) {
            $page = 12;
        }
        $this->paginate = $page;
    }

    /**
     * @param  int  $order
     */
    public function orderByFilter($order)
    {
        $this->orderByFilter = $order;
    }

    /**
     * @param $date
     * @param $id
     */
    public function updateTaskDueDate($date, $id)
    {
        $task = Task::findOrFail($id);
        if (! empty($date)) {
            $task->update([
                'due_date' => $date,
            ]);
        }
        $this->resetPage();
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
