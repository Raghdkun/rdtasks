<?php

namespace App\Http\Livewire;

use App\Models\Invoice;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Livewire\WithPagination;
use Str;

class Invoices extends SearchableComponent
{
    use WithPagination;

    public $search = '';

    public $statusFilter = 1;

    public $filterDueDate = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $invoices = $this->searchInvoice($this->search);
        $sentInvoiceCount = Invoice::where('status', Invoice::STATUS_SENT)->count();
        $totalInvoices = Invoice::count();

        return view('livewire.invoices', [
            'invoices' => $invoices,
        ], compact('sentInvoiceCount', 'totalInvoices'))->with('search');
    }

    /**
     * @param  string  $search
     * @return LengthAwarePaginator
     */
    public function searchInvoice($search)
    {
        /** @var Builder $project */
        $this->setQuery($this->getQuery()->with(['invoiceClients', 'invoiceProjects']));

        $this->getQuery()->where(function (Builder $query) {
            $this->filterResults();
        });

        $this->getQuery()->when($this->statusFilter !== '', function (Builder $q) {
            $q->where('status', $this->statusFilter);
        });

        $this->getQuery()->when(! empty($this->filterDueDate) && $this->filterDueDate == Invoice::DUE_DATE_ASC,
            function (Builder $q) {
                $q->orderBy('due_date', 'ASC');
            });

        $this->getQuery()->when(! empty($this->filterDueDate) && $this->filterDueDate == Invoice::DUE_DATE_DESC,
            function (Builder $q) {
                $q->orderBy('due_date', 'DESC');
            });

        return $this->paginate($withoutSearching = false);
    }

    protected $listeners = [
        'filterTasksByStatus',
        'filterDueDate',
    ];

    public function filterTasksByStatus($id)
    {
        $this->statusFilter = $id;
    }

    public function filterDueDate($id)
    {
        $this->filterDueDate = $id;
    }

    public function model()
    {
        return Invoice::class;
    }

    public function searchableFields()
    {
        return [
            'name',
            'amount',
            'invoice_number',
            'invoiceProjects.name',
            'invoiceClients.name',
        ];
    }

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
