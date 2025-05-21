<?php

namespace App\Http\Livewire;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Users extends SearchableComponent
{
    public $role = null;

    public $filterStatus = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $users = $this->searchUser($this->role);
        $totalUsers = User::count();

        return view('livewire.users', [
            'users' => $users,
            'totalUsers' => $totalUsers,
        ])->with('search');
    }

    /**
     * @param  string  $role
     * @return LengthAwarePaginator
     */
    public function searchUser($role)
    {
        $this->setQuery($this->getQuery()->with('roles', 'media', 'client')->where('owner_id', null)->where('owner_type', null)->orderByDesc('is_active')->withCount('projects',
            'taskAssignee', 'userActiveTask'));

        $this->getQuery()->where(function (Builder $query) {
            $this->filterResults();
        });

        $this->getQuery()->when($this->filterStatus != '', function (Builder $q) {
            if ($this->filterStatus == User::ARCHIVED) {
                $q->withTrashed()->where('deleted_at', '!=', null);
            } else {
                $q->where('is_active', '=', $this->filterStatus);
            }
        });

        $this->getQuery()->when(! empty($role), function (Builder $q) use ($role) {
            $q->WhereHas('roles', function (Builder $q) use ($role) {
                $q->where('name', '=', $role);
            });
        });

        return $this->paginate();
    }

    /**
     * @param $id
     */
    public function filterUsers($id)
    {
        $this->filterStatus = $id;
        $this->resetPage();
    }

    public function model()
    {
        return User::class;
    }

    public function searchableFields()
    {
        return [
            'name',
            'email',
            'roles.name',
        ];
    }

    protected $listeners = [
        'refresh' => '$refresh',
        'filterUsers',
    ];

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
                            $query->WhereRaw("lower($field[1]) like ?", $searchString);
                        });
                        $q->where('owner_id', null)->where('owner_type', null);
                    } else {
                        $q->orWhereRaw("lower($field) like ?", $searchString);
                    }
                }
            });
        });

        return $this->getQuery();
    }
}
