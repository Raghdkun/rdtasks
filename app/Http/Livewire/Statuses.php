<?php

namespace App\Http\Livewire;

use App\Models\Status;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;

class Statuses extends SearchableComponent
{
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * @return Application|Factory|View
     */
    public function render()
    {
        $status = $this->searchProject();
        $totalStatus = Status::count();

        return view('livewire.statuses', [
            'status' => $status,
            'totalStatus' => $totalStatus,
        ])->with('search');
    }

    /**
     * @return LengthAwarePaginator
     */
    public function searchProject()
    {
        $this->setQuery($this->getQuery());

        return $this->paginate();
    }

    public function model()
    {
        return Status::class;
    }

    /**
     * @var string[]
     */
    protected $listeners = [
        'refresh' => '$refresh',
    ];

    public function searchableFields()
    {
        return [
            'name',
        ];
    }
}
