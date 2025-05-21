<?php

namespace App\Http\Livewire;

use App\Models\Role;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class Roles extends SearchableComponent
{
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $roles = $this->searchRoles();

        return view('livewire.roles', [
            'roles' => $roles,
        ])->with('search');
    }

    /**
     * @return LengthAwarePaginator
     */
    public function searchRoles()
    {
        $this->setQuery($this->getQuery()->withCount('permissions'));

        return $this->paginate();
    }

    public function model()
    {
        return Role::class;
    }

    public function searchableFields()
    {
        return [
            'name',
            'description',
        ];
    }
}
