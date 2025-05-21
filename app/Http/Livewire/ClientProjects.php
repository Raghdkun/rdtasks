<?php

namespace App\Http\Livewire;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Livewire\WithPagination;
use Str;

class ClientProjects extends SearchableComponent
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
        $totalProjects = Project::where('client_id', getLoggedInUser()->owner_id)->count();

        return view('livewire.client-projects',
            ['projects' => $projects, 'totalProjects' => $totalProjects])->with('search');
    }

    /**
     * @return LengthAwarePaginator
     */
    public function searchProjects()
    {
        $this->setQuery($this->getQuery()->with(['client', 'tasks', 'users.media'])->where('client_id',
            getLoggedInUser()->owner_id)->withCount([
                'tasks' => function ($query) {
                    $query->where('status', '=', Task::$status['STATUS_ACTIVE']);
                }, 'users',
            ]));

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

    public function searchableFields()
    {
        return [
            'name',
            'prefix',
        ];
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
