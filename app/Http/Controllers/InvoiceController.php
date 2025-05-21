<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Report;
use App\Models\Task;
use App\Models\User;
use App\Repositories\InvoiceRepository;
use Barryvdh\DomPDF\Facade as PDF;
use Exception;
use Flash;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\HigherOrderBuilderProxy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\HigherOrderCollectionProxy;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class InvoiceController extends AppBaseController
{
    /** @var InvoiceRepository */
    private $invoiceRepository;

    public function __construct(InvoiceRepository $invoiceRepo)
    {
        $this->invoiceRepository = $invoiceRepo;
    }

    /**
     * Display a listing of the Invoice.
     *
     * @param  Request  $request
     * @return Application|Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request)
    {
        $status = Invoice::STATUS;
        $dueDateFilter = Invoice::ORDER_BY_DUE_DATE;

        return view('invoices.index', compact('status', 'dueDateFilter'));
    }

    /**
     * @param  CreateInvoiceRequest  $request
     * @return JsonResponse
     *
     * @throws Throwable
     */
    public function store(CreateInvoiceRequest $request)
    {
        try {
            DB::beginTransaction();
            $invoice = $this->invoiceRepository->saveInvoice($request->all());
            DB::commit();

            Flash::success('Invoice saved successfully.');

            return $this->sendResponse($invoice, 'Invoice created successfully.');
        } catch (Exception $e) {
            DB::rollBack();

            return $this->sendError($e->getMessage());
        }
    }

    /**
     * @param  Invoice  $invoice
     * @return Application|Factory|View
     */
    public function show(Invoice $invoice)
    {
        $invoice = $this->invoiceRepository->getSyncListForInvoiceDetail($invoice->id);

        return view('invoices.show', compact('invoice'));
    }

    /**
     * @param  Invoice  $invoice
     * @return RedirectResponse
     */
    public function edit(Invoice $invoice)
    {
        if ($invoice->status == Invoice::STATUS_PAID || Auth::user()->hasRole('Client')) {
            return redirect()->back();
        }

        $data['invoiceSyncList'] = $this->invoiceRepository->getSyncList($invoice);

        $invoice = Invoice::with([
            'invoiceProjects', 'invoiceClients.projects', 'invoiceItems.task.timeEntries', 'invoiceItems.task.project',
        ])->find($invoice->id);

        $data['countFixRate'] = $this->invoiceRepository->countFixRate($invoice);

        return view('invoices.edit', $data, compact('invoice'));
    }

    /**
     * @param  Invoice  $invoice
     * @param  UpdateInvoiceRequest  $request
     * @return JsonResponse
     */
    public function update(Invoice $invoice, UpdateInvoiceRequest $request)
    {
        try {
            DB::beginTransaction();
            $invoice = $this->invoiceRepository->updateInvoice($request->all(), $invoice->id);
            DB::commit();

            Flash::success('Invoice update successfully.');

            return $this->sendResponse($invoice, 'Invoice update successfully.');
        } catch (Exception $e) {
            DB::rollBack();

            return $this->sendError($e->getMessage());
        }
    }

    /**
     * @param  Invoice  $invoice
     * @return JsonResponse|RedirectResponse
     *
     * @throws Throwable
     */
    public function destroy(Invoice $invoice)
    {
        try {
            DB::beginTransaction();
            $reportId = $invoice->invoiceReport()->value('report_id');

            /** @var Report $report */
            $report = Report::whereId($reportId)->update(['invoice_generate' => 0]);

            $invoice->invoiceItems()->delete();
            $invoice->invoiceReport()->sync([]);
            $invoice->delete();
            DB::commit();

            return $this->sendSuccess('Invoice Deleted successfully.');
        } catch (Exception $e) {
            DB::rollBack();

            return Redirect::back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * @param  Project  $project
     * @return JsonResponse
     */
    public function getProjectTasks(Project $project)
    {
        $tasks = Task::whereProjectId($project->id)->pluck('title', 'id');

        return $this->sendResponse($tasks, 'Task Retrieved Successfully.');
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function getTaskDetails($id)
    {
        $task = Task::with('timeEntries')->findOrFail($id);
        $minutes = $task->timeEntries->pluck('duration')->sum();
        $hours = floor($minutes / 60);
        $min = $minutes - ($hours * 60);
        $task->duration = $hours.'.'.$min;

        $task->description = strip_tags(htmlspecialchars_decode($task->description));

        return $this->sendResponse($task, 'Project Retrieved Successfully.');
    }

    /**
     * @param  Client  $client
     * @return HigherOrderBuilderProxy|HigherOrderCollectionProxy|mixed
     */
    public function getClientProjects(Client $client)
    {
        $projects = Project::whereClientId($client->id)->pluck('name', 'id');

        return $this->sendResponse($projects, 'Project Retrieved Successfully.');
    }

    /**
     * @param  Invoice  $invoice
     * @return RedirectResponse|Redirector
     */
    public function convertToPdf(Invoice $invoice)
    {
        $user = getLoggedInUser();
        if ($user->hasRole('Client')) {
            $invoiceIds = $invoice->invoiceClients()->where('client_id',
                $user->owner_id)->pluck('invoice_id')->toArray();
            if (! in_array($invoice->id, $invoiceIds)) {
                return redirect(route('client.invoices.index'));
            }
        } elseif (! $user->can('manage_invoices')) {
            return \redirect()->back();
        }

        $invoice = Invoice::with('invoiceItems.task.project', 'invoiceClients',
            'invoiceProjects')->findOrFail($invoice->id);
        $data['setting'] = $this->invoiceRepository->getSyncListForSetting();
        $invoiceTemplate = $data['setting']['default_invoice_template'];
        $data['invoice'] = $invoice;

        $pdf = PDF::loadView("invoices.invoice_template_pdf.$invoiceTemplate", $data);

        return $pdf->stream('Invoice Number '.$invoice->invoice_number.'.pdf');
    }

    /**
     * @param $invoiceName
     * @return BinaryFileResponse
     */
    public function downloadInvoice($invoiceName)
    {
        /* @var User $user */
        $user = Auth::user();
        if ($user->can('manage_invoices')) {
            return response()->download(storage_path("app/public/invoice/{$invoiceName}"));
        }
    }

    /**
     * @param  Report  $report
     * @return Factory|View
     */
    public function createInvoice(Report $report)
    {
        
        $data = $this->invoiceRepository->prepareData($report->id);
        $data['report'] = $report;

        return view('invoices.create', $data);
    }

    /**
     * @param  Invoice  $invoice
     * @return JsonResponse
     */
    public function updateStatus(Invoice $invoice)
    {
        if (! empty($invoice->id)) {
            $invoice->update(['status' => Invoice::STATUS_PAID]);
        } else {
            return $this->sendError('Invoice not found.');
        }

        return $this->sendSuccess('Invoice Status updated.');
    }
}
