<?php

namespace App\Http\Livewire;

use App\Models\Tax;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class Taxes extends SearchableComponent
{
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $taxes = $this->searchTaxes();
        $totalTaxes = Tax::count();

        return view('livewire.taxes', [
            'taxes' => $taxes,
            'totalTaxes' => $totalTaxes,
        ])->with('search');
    }

    protected $listeners = ['filterDepartment', 'refresh' => '$refresh'];

    /**
     * @return LengthAwarePaginator
     */
    public function searchTaxes()
    {
        $this->setQuery($this->getQuery());

        return $this->paginate();
    }

    /**
     * @return mixed|string
     */
    public function model()
    {
        return Tax::class;
    }

    /**
     * @return mixed|string[]
     */
    public function searchableFields()
    {
        return [
            'name',
            'tax',
        ];
    }
}
