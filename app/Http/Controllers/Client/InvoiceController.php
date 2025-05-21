<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\AppBaseController;
use App\Models\Invoice;
use App\Repositories\InvoiceRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceController extends AppBaseController
{
    /** @var InvoiceRepository */
    private $invoiceRepository;

    public function __construct(InvoiceRepository $invoiceRepo)
    {
        $this->invoiceRepository = $invoiceRepo;
    }

    /**
     * @param  Request  $request
     * @return Application|Factory|View
     */
    public function index(Request $request)
    {
        $status = Invoice::CLIENT_STATUS;
        $dueDateFilter = Invoice::ORDER_BY_DUE_DATE;

        return view('client_panel.invoices.index', compact('status', 'dueDateFilter'));
    }

    /**
     * @param  Invoice  $invoice
     * @return Application|Factory|View
     */
    public function show(Invoice $invoice)
    {
        $invoiceIds = $invoice->invoiceClients()->where('client_id',
            getLoggedInUser()->owner_id)->pluck('invoice_id')->toArray();
        if (! in_array($invoice->id, $invoiceIds)) {
            return redirect(route('client.invoices.index'));
        }
        $invoice = $this->invoiceRepository->getSyncListForInvoiceDetail($invoice->id);

        return view('client_panel.invoices.show', compact('invoice'));
    }

    /**
     * @param  Invoice  $invoice
     * @return Application|Factory|RedirectResponse|View
     */
    public function edit(Invoice $invoice)
    {
        if ($invoice->status == Invoice::STATUS_PAID) {
            return \redirect()->back();
        }

        $data['invoiceSyncList'] = $this->invoiceRepository->getSyncList($invoice);

        $invoice = Invoice::with([
            'invoiceProjects', 'invoiceClients.projects', 'invoiceItems.task.timeEntries', 'invoiceItems.task.project',
        ])->find($invoice->id);

        $data['countFixRate'] = $this->invoiceRepository->countFixRate($invoice);

        return view('client_panel.invoices.edit', $data, compact('invoice'));
    }

    /**
     * @param  Invoice  $invoice
     * @param  Request  $request
     * @return JsonResponse
     */
    public function changeStatus(Invoice $invoice, Request $request)
    {
        Invoice::whereId($invoice->id)->update(['status' => $request->invoiceStatus]);

        return $this->sendSuccess('Status updated Successfully');
    }
}
