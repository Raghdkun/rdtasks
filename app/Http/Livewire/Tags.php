<?php

namespace App\Http\Livewire;

use App\Models\Tag;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class Tags extends SearchableComponent
{
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $tags = $this->searchProject();
        $totalTags = Tag::count();

        return view('livewire.tags', [
            'tags' => $tags,
            'totalTags' => $totalTags,
        ])->with('search');
    }

    protected $listeners = ['refresh' => '$refresh'];

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
        return Tag::class;
    }

    public function searchableFields()
    {
        return [
            'name',
        ];
    }
}
