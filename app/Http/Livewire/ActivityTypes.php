<?php

namespace App\Http\Livewire;

use App\Models\ActivityType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ActivityTypes extends SearchableComponent
{
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $activityTypes = $this->searchActivityTypes();
        $totalActivityTypes = ActivityType::count();

        return view('livewire.activity-types', [
            'activityTypes' => $activityTypes,
            'totalActivityTypes' => $totalActivityTypes,
        ])->with('search');
    }

    protected $listeners = ['filterDepartment', 'refresh' => '$refresh'];

    /**
     * @return LengthAwarePaginator
     */
    public function searchActivityTypes()
    {
        $this->setQuery($this->getQuery());

        return $this->paginate();
    }

    public function model()
    {
        return ActivityType::class;
    }

    public function searchableFields()
    {
        return [
            'name',
        ];
    }
}
