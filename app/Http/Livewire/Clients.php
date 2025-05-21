<?php

namespace App\Http\Livewire;

use App\Models\Client;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Clients extends SearchableComponent
{
    public $department = null;

    public $departmentFilter = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $clients = $this->searchClients($this->department);
        $totalClients = Client::count();

        return view('livewire.clients', [
            'clients' => $clients,
            'totalClients' => $totalClients,
        ])->with('search');
    }

    /**
     * @param $department
     * @return LengthAwarePaginator
     */
    public function searchClients($department)
    {
        $this->setQuery($this->getQuery()->with('department', 'media')->orderBy('name', 'asc'));

        $this->getQuery()->where(function (Builder $query) {
            $this->filterResults();
        });

        $this->getQuery()->when(! empty($this->departmentFilter), function (Builder $q) {
            $q->WhereHas('department', function (Builder $q) {
                $q->where('id', $this->departmentFilter);
            });
        });

        return $this->paginate($withoutSearching = false);
    }

    protected $listeners = ['filterDepartment', 'refresh' => '$refresh'];

    /**
     * @param $department
     */
    public function filterDepartment($department)
    {
        $this->departmentFilter = $department;
        $this->resetPage();
    }

    /**
     * @return mixed|string
     */
    public function model()
    {
        return Client::class;
    }

    /**
     * @return mixed|string[]
     */
    public function searchableFields()
    {
        return [
            'name',
            'email',
            'website',
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
