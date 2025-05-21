<?php

namespace App\Http\Livewire;

use App\Models\Department;

class Departments extends SearchableComponent
{
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function render()
    {
        $departments = $this->searchDepartments();
        $totalDepartments = Department::count();

        return view('livewire.departments', [
            'departments' => $departments,
            'totalDepartments' => $totalDepartments,
        ])->with('search');
    }

    protected $listeners = ['refresh' => '$refresh'];

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function searchDepartments()
    {
        return $this->paginate();
    }

    /**
     * @return string
     */
    public function model()
    {
        return Department::class;
    }

    /**
     * @return array
     */
    public function searchableFields()
    {
        return [
            'name',
        ];
    }
}
