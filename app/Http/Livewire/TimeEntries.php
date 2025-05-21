<?php

namespace App\Http\Livewire;

use App\Models\TimeEntry;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class TimeEntries extends Component
{
    use WithPagination;

    public $search = '';

    public $paginate = 10;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * @return Factory|View
     */
    public function render()
    {
        $timeEntries = $this->searchTimeEntry($this->search);

        return view('livewire.time-entries', [
            'timeEntries' => $timeEntries,
        ]);
    }

    /**
     * @param  string  $search
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function searchTimeEntry($search)
    {
        /** @var Builder $timeEntry */
        $timeEntry = TimeEntry::query();

        $timeEntry->when(! empty($search), function (Builder $q) use ($search) {
            $q->whereHas('task', function (Builder $q) use ($search) {
                $q->where('title', 'like', '%'.$search.'%')
                    ->orWhereHas('project', function (Builder $q) use ($search) {
                        $q->where('prefix', 'like', '%'.$search.'%')
                            ->orWhere('name', 'like', '%'.$search.'%');
                    });
            });
            $q->orWhereHas('activityType', function (Builder $q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%');
            });
        });

        return $timeEntry->paginate($this->paginate);
    }
}
