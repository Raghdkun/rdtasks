<?php

namespace App\Repositories;

use App\Mail\InvoiceSendToClient;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Project;
use App\Models\Report;
use App\Models\ReportFilter;
use App\Models\Setting;
use App\Models\Task;
use App\Models\Tax;
use App\Models\UserNotification;
use Auth;
use Exception;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Mail;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use URL;

/**
 * Class InvoiceRepository
 *
 *
 * @version April 8, 2020, 11:32 am UTC
 */
class InvoiceRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'project_id',
        'invoice_number',
        'issue_date',
        'due_date',
        'client_id',
        'notes',
    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Invoice::class;
    }

    /**
     * @return array
     */
    public function getDiscountTypes()
    {
        return $discountType = [
            '0' => 'No Discount',
            '1' => 'Before Tax',
            '2' => 'After Tax',
        ];
    }

    /**
     * @param  Invoice  $invoice
     * @return mixed
     */
    public function getSyncList($invoice = null)
    {
        $data['projects'] = Project::toBase()->pluck('name', 'id')->toArray();
        $data['clients'] = Client::toBase()->pluck('name', 'id')->toArray();
        $data['discountType'] = $this->getDiscountTypes();
        $taxes = Tax::toBase()->orderBy('tax', 'asc')->get();
        $data['taxes'] = [];
        foreach ($taxes as $tax) {
            $data['taxes'][$tax->id] = $tax->name.' - '.'{'.$tax->tax.' %}';
        }
        $data['taxesArr'] = $taxes->pluck('tax', 'id')->toArray();
        $data['clientIds'] = $invoice->invoiceClients->pluck('id')->toArray();

        return $data;
    }

    /**
     * @param  array  $input
     * @return Invoice
     */
    public function saveInvoice($input)
    {
        try {

            /** @var Report $report */
            $report = Report::with('reportInvoice')->whereId($input['report_id'])->first();
            $report->update(['invoice_generate' => 1]);
            $report->reportInvoice()->delete();

            /** @var Invoice $invoice */
            $input['due_date'] = isset($input['due_date']) ? $input['due_date'] : null;
            $invoice = $this->create($this->prepareInvoiceData($input));
            $invoice->invoiceReport()->sync($input['report_id']);
            $invoice->invoiceClients()->sync($input['client_id']);
            $invoice->invoiceProjects()->sync($input['project_id']);

            if (! empty($input['project_id'])) {
                foreach ($input['project_id'] as $projectId) {
                    $projects = Project::find($projectId);
                    activity()
                        ->causedBy(getLoggedInUser())
                        ->withProperties(['modal' => Invoice::class, 'data' => 'of '.$projects->name])
                        ->performedOn($projects)
                        ->useLog('Invoice Created')
                        ->log('Created project invoice');

                    if (! empty($input['client_id'])) {
                        foreach ($input['client_id'] as $clientId) {
                            $client = Client::find($clientId);
                            if (! is_null($client->user_id)) {
                                UserNotification::create([
                                    'title' => 'New Invoice Created',
                                    'description' => 'Invoice created for '.$projects->name,
                                    'type' => Invoice::class,
                                    'user_id' => $client->user_id,
                                ]);
                            }
                        }
                    }
                }
            }
            // Store Items
            $this->storeInvoiceItems($input, $invoice);
            if ($invoice->status == Invoice::STATUS_SENT) {
                $invoice->invoiceItems;
                $data = $this->getSyncListForCreate($invoice->id);
                $data['invoice'] = $invoice;

                $data['invoiceUrl'] = URL::to('invoices/'.$invoice->id.'/pdf');
                $clientIds = $invoice->invoiceClients()->pluck('client_id');
                $data['clientNames'] = Client::whereIn('id', $clientIds)->pluck('name')->toArray();

                $projectIds = $invoice->invoiceProjects()->pluck('project_id');
                $data['projectNames'] = Project::whereIn('id', $projectIds)->pluck('name')->toArray();

                $data['invoiceNumber'] = $invoice->invoice_number;

                $emails = Client::whereIn('id', $clientIds)->pluck('email')->toArray();

                foreach ($emails as $email) {
                    if (! empty($email)) {
                        Mail::to($email)
                            ->send(new InvoiceSendToClient('emails.invoice_send_client.invoice_send_client',
                                'New Invoice Created',
                                $data));
                    }
                }
            }
        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }

        return $invoice;
    }

    /**
     * @param  array  $input
     * @param  Invoice  $owner
     * @return bool
     */
    public function storeInvoiceItems($input, $owner)
    {
        $owner->invoiceItems()->delete();
        foreach ($input['itemsArr'] as $record) {
            $data['owner_id'] = $owner->getId();
            $data['owner_type'] = $owner->getOwnerType();
            $data['task_id'] = $record['task_id'];
            $data['fix_rate'] = $record['fix_rate'];
            $data['item_project_id'] = $record['item_project_id'];
            $data = array_merge($record, $data);
            $invoiceItem = InvoiceItem::create($data);

            $data = [];
        }

        return true;
    }

    /**
     * @param  array  $input
     * @return array
     */
    public function prepareInvoiceData($input)
    {
        $invoiceFields = (new Invoice())->getFillable();
        $items = [];
        foreach ($input as $key => $value) {
            if (in_array($key, $invoiceFields)) {
                $items[$key] = $value;
            }
        }
        $items['amount'] = formatNumber($input['amount']);
        $items['discount'] = formatNumber($input['discount']);
        $items['sub_total'] = formatNumber($input['sub_total']);
        $items['status'] = $input['invoice_status'];
        $items['created_by'] = Auth::id();

        return $items;
    }

    /**
     * @param  array  $input
     * @param  int  $id
     * @return Invoice
     */
    public function updateInvoice($input, $id)
    {
        /** @var Invoice $invoice */
        $invoice = Invoice::find($id);
        $input['due_date'] = isset($input['due_date']) ? $input['due_date'] : null;
        $invoice->update($this->prepareInvoiceData($input));

        // Update Items
        $this->storeInvoiceItems($input, $invoice);
        if ($invoice->status == Invoice::STATUS_SENT) {
            $invoice->invoiceItems;
            $data = $this->getSyncListForCreate($invoice->id);
            $data['invoice'] = $invoice;

            $data['invoiceUrl'] = URL::to('invoices/'.$invoice->id.'/pdf');
            $clientIds = $invoice->invoiceClients()->pluck('client_id');
            $data['clientNames'] = Client::whereIn('id', $clientIds)->pluck('name')->toArray();

            $projectIds = $invoice->invoiceProjects()->pluck('project_id');
            $data['projectNames'] = Project::whereIn('id', $projectIds)->pluck('name')->toArray();

            $data['invoiceNumber'] = $invoice->invoice_number;

            $emails = Client::whereIn('id', $clientIds)->pluck('email')->toArray();

            foreach ($emails as $email) {
                if (! empty($email)) {
                    Mail::to($email)
                        ->send(new InvoiceSendToClient('emails.invoice_send_client.invoice_send_client',
                            'New Invoice Created',
                            $data));
                }
            }
        }

        return $invoice;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getSyncListForInvoiceDetail($id)
    {
        return Invoice::with(['invoiceProjects', 'invoiceClients.projects', 'invoiceItems.task.project'])->find($id);
    }

    /**
     * @param  null  $invoiceId
     * @return mixed
     */
    public function getSyncListForCreate($invoiceId = null)
    {
        $data['setting'] = Setting::all()->pluck('value', 'key')->toArray();

        return $data;
    }

    /**
     * @return mixed
     */
    public function getSyncListForSetting()
    {
        $setting = Setting::pluck('value', 'key')->toArray();

        return $setting;
    }

    /**
     * @param  int  $id
     * @return mixed
     */
    public function prepareData($id)
    {
        $reportUserIds = ReportFilter::whereReportId($id)->where('param_type', \App\Models\User::class)->pluck('param_id');
        /** @var ReportRepository $reportRepo */
        $reportRepo = app(ReportRepository::class);
        $data['projectIds'] = $reportRepo->getProjectIds($id);
        $data['departmentId'] = $reportRepo->getDepartmentId($id);

        $report = Report::findOrFail($id);
        $date['reportStartDate'] = $report->start_date->format('Y-m-d');
        $date['reportEndDate'] = $report->end_date->format('Y-m-d');

        $project = Project::findOrFail($data['projectIds'])->first();

        /** @var ProjectRepository $projectRepo */
        $projectRepo = app(ProjectRepository::class);
        $data['clientId'] = Client::with('projects')->whereHas('projects', function ($query) use ($data) {
            $query->whereIn('id', $data['projectIds']);
        })->pluck('id')->toArray();
        $data['projects'] = $projectRepo->getProjectsByClients($data['clientId']);
        $data['project'] = $project;

        /** @var ClientRepository $clientRepo */
        $clientRepo = app(ClientRepository::class);
        $data['clients'] = Client::orderBy('name')->whereIn('department_id', $data['departmentId'])->pluck('name',
            'id');

        $taxes = Tax::orderBy('tax', 'asc')->get();
        $data['taxes'] = [];
        foreach ($taxes as $tax) {
            $data['taxes'][$tax->id] = $tax->name.' - '.'{'.$tax->tax.' %}';
        }
        $data['taxesArr'] = $taxes->pluck('tax', 'id')->toArray();

        $data['tasks'] = Task::with([
            'project.client', 'timeEntries' => function (HasMany $query) use ($date, $reportUserIds) {
                $query->whereRaw('date(start_time) >= ? and date(end_time) <= ?',
                    [$date['reportStartDate'], $date['reportEndDate']]);
                $query->whereIn('user_id', $reportUserIds);
            },
        ])->whereIn('project_id', $data['projectIds'])->get();

        $minutes = 0;
        $data['projectIds'] = [];
        $data['clientId'] = [];
        foreach ($data['tasks'] as $task) {
            if (count($task->timeEntries) > 0) {
                if (! in_array($task->project->id, $data['projectIds'])) {
                    $data['projectIds'][] = $task->project->id;
                }
                if (! in_array($task->project->client->id, $data['clientId'])) {
                    $data['clientId'][] = $task->project->client->id;
                }
            }

            foreach ($task->timeEntries as $timeEntry) {
                $minutes += $timeEntry->duration;
            }
        }
        $data['totalHours'] = $this->calculateHours($minutes);
        $data['discountType'] = $this->getDiscountTypes();
        $data['fixRate'] = $this->createCountFixRate($data['projectIds']);

        if ($report->report_type == Report::STATIC_REPORT) {
            $data['taskMeta'] = [];
            $taskMetaArray = [];
            $projectIds = [];
            $countDuration = 0;
            $reportData = json_decode($report->report_data, true);
            foreach ($reportData as $key => $reportDatum) {
                foreach ($reportDatum['clients'] as $clientKey => $client) {
                    foreach ($client['projects'] as $projectKey => $projectData) {
                        $projectIds[] = $projectKey;
                        $project = Project::whereId($projectKey)->first();
                        foreach (array_values($projectData['users'])[0]['tasks'] as $taskKey => $task) {
                            $taskNumber = Task::whereId($taskKey)->value('task_number');
                            $taskMetaArray[$taskKey]['name'] = $task['name'];
                            $taskMetaArray[$taskKey]['duration'] = $task['duration'];
                            $taskMetaArray[$taskKey]['task_total_hour'] = staticTaskTotalHours($task['duration']);
                            $taskMetaArray[$taskKey]['task_id'] = $task['task_id'];
                            $taskMetaArray[$taskKey]['task_number'] = $taskNumber;
                            $taskMetaArray[$taskKey]['project'] = $project;
                            $countDuration += $task['duration'];
                        }
                    }
                }
            }
            $data['totalHours'] = $this->calculateHours($countDuration);
            $data['fixRate'] = $this->createCountFixRate($projectIds);
            $data['taskMeta'] = array_values($taskMetaArray);
        }

        return $data;
    }

    /**
     * @param $projectIds
     * @return mixed
     */
    public function createCountFixRate($projectIds)
    {
        $CountFixRate = Project::whereIn('id', $projectIds)->where('budget_type', Project::FIXED_COST)->sum('price');

        return $CountFixRate;
    }

    /**
     * @param  null  $invoice
     * @return mixed
     */
    public function getSyncTaskList($invoice)
    {
        /** @var Invoice $invoice */
        $reportId = $invoice->invoiceReport()->value('report_id');

        /** @var ReportRepository $reportRepo */
        $reportRepo = app(ReportRepository::class);
        $data['projectIds'] = $reportRepo->getProjectIds($reportId);
        $data['departmentId'] = $reportRepo->getDepartmentId($reportId);
        $report = Report::findOrFail($reportId);
        $date['reportStartDate'] = $report->start_date->format('Y-m-d');
        $date['reportEndDate'] = $report->end_date->format('Y-m-d');
        $data['report'] = $report;

        $project = Project::findOrFail($data['projectIds'])->first();

        $data['tasks'] = Task::with([
            'project', 'timeEntries' => function (HasMany $query) use ($date) {
                $query->whereRaw('date(start_time) >= ? and date(end_time) <= ?',
                    [$date['reportStartDate'], $date['reportEndDate']]);
            },
        ])->where('project_id', $project->id)->get();

//        $data['tasks'] = Task::with('project', 'timeEntries')->whereProjectId($project->id)->get();
        $minutes = 0;
        foreach ($data['tasks'] as $task) {
            foreach ($task->timeEntries as $timeEntry) {
                $minutes += $timeEntry->duration;
            }
        }

        $data['totalHours'] = $this->calculateHours($minutes);

        return $data;
    }

    /**
     * @param $minutes
     * @return string
     */
    public function calculateHours($minutes)
    {
        $hours = floor($minutes / 60);
        $min = $minutes - ($hours * 60);

        return $hours.'.'.$min;
    }

    /**
     * @param $invoice
     * @return string
     */
    public function countFixRate($invoice)
    {
        $projectsIds = [];
        foreach ($invoice->invoiceItems as $invoiceItem) {
            if (isset($invoiceItem->item_project_id) && ! empty($invoiceItem->item_project_id)) {
                $projectsIds[$invoiceItem->item_project_id] = $invoiceItem->item_project_id;
            }
        }
        $countOldFixRate = 0;
        foreach ($projectsIds as $projectsId) {
            $countOldFixRate += InvoiceItem::whereItemProjectId($projectsId)->first()->fix_rate;
        }

        return $countOldFixRate;
    }
}
