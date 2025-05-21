<?php

namespace App\Http\Livewire;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\WithPagination;

class UserProjects extends SearchableComponent
{
    use WithPagination;

    public $projectStatus = '1';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $projects = $this->searchProjects();
        $totalProjects = Project::whereHas('users', function (Builder $q) {
            $q->where('user_id', '=', Auth::id());
        })->count();

        return view('livewire.user-projects', [
            'projects' => $projects,
            'totalProjects' => $totalProjects,
        ])->with('search');
    }

    /**
     * @return LengthAwarePaginator
     */
    public function searchProjects()
    {
        $this->setQuery($this->getQuery()->with(['client', 'tasks', 'users.media'])->withCount([
            'tasks' => function ($query) {
                $query->where('status', '=', Task::$status['STATUS_ACTIVE']);
            }, 'users',
        ]));

        $this->getQuery()->whereHas('users', function (Builder $q) {
            $q->where('user_id', '=', Auth::id());
        });

        $this->getQuery()->where(function (Builder $query) {
            $this->filterResults();
        });

        $this->getQuery()->when(! empty($this->projectStatus), function (Builder $q) {
            $q->where('status', $this->projectStatus);
        });

        return $this->paginate($withoutSearching = false);
    }

    protected $listeners = [
        'refresh' => '$refresh',
        'projectsStatus',
    ];

    /**
     * @param $status
     */
    public function projectsStatus($status)
    {
        $this->projectStatus = $status;
    }

    public function model()
    {
        return Project::class;
    }

    /**
     * @return mixed|string[]
     */
    public function searchableFields()
    {
        return [
            'name',
            'prefix',
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
