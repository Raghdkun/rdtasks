<?php

namespace App\Http\Livewire;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProjectDetailsTasks extends SearchableComponent
{
    public $projectId;

    public $paginate = 15;

    public function mount($projectId)
    {
        $this->projectId = $projectId;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * @return Application|Factory|View
     */
    public function render()
    {
        $projectTasks = $this->searchTasks();
        $project = Project::find($this->projectId);
        if (authUserHasPermission('manage_projects') || getLoggedInUser()->hasRole('Client')) {
            $totalTasks = Task::whereProjectId($this->projectId)->where('status', '!=',
                Task::STATUS_COMPLETED)->count();
        } else {
            $totalTasks = Task::whereProjectId($this->projectId)->whereHas('taskAssignee',
                function (Builder $query) {
                    $query->where('user_id', getLoggedInUserId());
                })->where('status', '!=', Task::STATUS_COMPLETED)->count();
        };

        return view('livewire.project-details-tasks', compact('projectTasks', 'project', 'totalTasks'));
    }

    /**
     * @return LengthAwarePaginator
     */
    public function searchTasks()
    {
        if (authUserHasPermission('manage_projects') || getLoggedInUser()->hasRole('Client')) {
            $query = $this->getQuery()->with(['project', 'timeEntries', 'taskAssignee.media'])->where('project_id', '=',
                $this->projectId)->where('status', '!=', Task::STATUS_COMPLETED)->orderBy('created_at', 'DESC');
        } else {
            $query = $this->getQuery()->with('timeEntries', 'taskAssignee.media', 'project')->whereHas('taskAssignee',
                function (Builder $query) {
                    $query->where('user_id', getLoggedInUserId());
                })->whereProjectId($this->projectId)->where('status', '!=',
                Task::STATUS_COMPLETED)->orderBy('created_at', 'DESC');
        }
        $this->setQuery($query);

        $this->getQuery()->where(function (Builder $query) {
            $this->filterResults();
        });

        return $this->paginate();
    }

    protected $listeners = [
        'refresh' => '$refresh',
    ];

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
        ];
    }

    /**
     * @return Builder
     */
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
