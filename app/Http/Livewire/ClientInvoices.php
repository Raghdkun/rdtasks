<?php

namespace App\Http\Livewire;

use App\Models\Invoice;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\WithPagination;

class ClientInvoices extends SearchableComponent
{
    use WithPagination;

    public $search = '';

    public $statusFilter = 1;

    public $filterDueDate = '';

    protected $listeners = [
        'filterTasksByStatus',
        'filterDueDate',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * @return Application|Factory|View
     */
    public function render()
    {
        $invoices = $this->searchInvoice($this->search);
        $sentInvoiceCount = Invoice::whereHas('invoiceClients', function (Builder $q) {
            $q->where('client_id', Auth::user()->owner_id);
        })->where('status', Invoice::STATUS_SENT)->count();
        $totalInvoices = Invoice::whereHas('invoiceClients', function (Builder $q) {
            $q->where('client_id', Auth::user()->owner_id);
        })->count();

        return view('livewire.client-invoices', [
            'invoices' => $invoices,
        ], compact('sentInvoiceCount', 'totalInvoices'))->with('search');
    }

    /**
     * @param $search
     * @return LengthAwarePaginator
     */
    public function searchInvoice($search)
    {

        /** @var Builder $project */
        $this->setQuery($this->getQuery()->with(['invoiceClients', 'invoiceProjects'])->where('status', '!=', Invoice::STATUS_DRAFT));

        $this->getQuery()->where(function (Builder $query) {
            $this->filterResults();
        });

        $this->getQuery()->whereHas('invoiceClients', function (Builder $q) {
            $q->where('client_id', '=', Auth::user()->owner_id);
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

    /**
     * @param  int  $id
     */
    public function filterTasksByStatus($id)
    {
        $this->statusFilter = $id;
    }

    /**
     * @param  int  $id
     */
    public function filterDueDate($id)
    {
        $this->filterDueDate = $id;
    }

    /**
     * @return mixed|string
     */
    public function model()
    {
        return Invoice::class;
    }

    /**
     * @return mixed|string[]
     */
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
