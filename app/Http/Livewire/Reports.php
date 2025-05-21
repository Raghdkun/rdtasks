<?php

namespace App\Http\Livewire;

use App\Models\Report;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class Reports extends Component
{
    use WithPagination;

    public $createdBy = null;

    public $search = '';

    public $paginate = 10;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $reports = $this->searchReports($this->search, $this->createdBy);

        return view('livewire.reports', [
            'reports' => $reports,
        ])->with('search');
    }

    /**
     * @param  string  $search
     * @param $createdBy
     * @return LengthAwarePaginator
     */
    public function searchReports($search, $createdBy)
    {
        $user = getLoggedInUser();

        /** @var Builder $query */
        $query = Report::with('user')->select('reports.*');

        if (! $user->hasPermissionTo('manage_reports')) {
            return $query->where('owner_id', $user->id);
        }

        $query->when(! empty($createdBy), function (Builder $query) use ($createdBy) {
            $query->where('owner_id', $createdBy);
        });

        $query->when(! empty($search), function (Builder $q) use ($search) {
            $q->where('name', 'like', '%'.$search.'%');
        });

        return $query->paginate($this->paginate);
    }

    protected $listeners = ['filterReportsCreatedBy'];

    /**
     * @param $createdById
     */
    public function filterReportsCreatedBy($createdById)
    {
        $this->createdBy = $createdById;
    }
}
