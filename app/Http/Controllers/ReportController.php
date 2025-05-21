<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateReportRequest;
use App\Http\Requests\UpdateReportRequest;
use App\Models\Invoice;
use App\Models\Report;
use App\Models\User;
use App\Queries\ReportDataTable;
use App\Repositories\ClientRepository;
use App\Repositories\DepartmentRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\ReportRepository;
use App\Repositories\TagRepository;
use App\Repositories\UserRepository;
use Arr;
use Auth;
use DataTables;
use Exception;
use Flash;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;
use Throwable;

/**
 * Class ReportController.
 */
class ReportController extends AppBaseController
{
    /** @var ReportRepository */
    private $reportRepository;

    /** @var UserRepository */
    private $userRepo;

    /** @var TagRepository */
    private $tagRepo;

    /** @var ClientRepository */
    private $clientRepo;

    /** @var ProjectRepository */
    private $projectRepo;

    /** @var DepartmentRepository */
    private $departmentRepo;

    public function __construct(
        ReportRepository $reportRepo,
        UserRepository $userRepository,
        ProjectRepository $projectRepository,
        ClientRepository $clientRepository,
        TagRepository $tagRepository,
        DepartmentRepository $departmentRepository
    ) {
        $this->reportRepository = $reportRepo;
        $this->userRepo = $userRepository;
        $this->clientRepo = $clientRepository;
        $this->tagRepo = $tagRepository;
        $this->projectRepo = $projectRepository;
        $this->departmentRepo = $departmentRepository;
    }

    /**
     * Display a listing of the Reports.
     *
     * @param  Request  $request
     * @return Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return Datatables::of((new ReportDataTable())->get($request->only(['filter_created_by'])))->make(true);
        }

        $users = [];

        if (Auth::user()->hasPermissionTo('manage_reports')) {
            $users = User::whereOwnerId(null)->whereOwnerType(null)->whereIsActive(true)->where('email_verified_at', '!=', null)->orderBy('name')->pluck('name', 'id');
        }

        return view('reports.index', compact('users'));
    }

    /**
     * Show the form for creating a new Report.
     *
     * @return Factory|View
     */
    public function create()
    {
        $data['tags'] = $this->tagRepo->getTagList();
        $data['users'] = $this->reportRepository->getUserList();
        $data['projects'] = $this->projectRepo->getProjectsList();
        $data['clients'] = $this->clientRepo->getClientList();
        $data['departments'] = $this->departmentRepo->getDepartmentList();

        return view('reports.create', $data);
    }

    /**
     * Store a newly created Report in storage.
     *
     * @param  CreateReportRequest  $request
     * @return RedirectResponse|Redirector
     */
    public function store(CreateReportRequest $request)
    {
        $input = $request->all();
        $input['owner_id'] = Auth::id();

        $report = $this->reportRepository->store($input);
        Flash::success('Report saved successfully.');

        return redirect(route('reports.show', $report->id));
    }

    /**
     * Display the specified Report.
     *
     * @param  Report  $report
     * @return Factory|View
     */
    public function show(Report $report)
    {
        $user = getLoggedInUser();
        if (! $user->hasPermissionTo('manage_reports') || ! $user->hasRole('Admin')) {
            if ($report->owner_id != $user->id) {
                return redirect()->back();
            }
        }

        $reports = $this->reportRepository->getReport($report);
        if ($report->report_type == Report::STATIC_REPORT) {
            if (! empty($report->report_data)) {
                $reports = json_decode($report->report_data, true);
            } else {
                $report->update([
                    'report_data' => json_encode($reports),
                ]);
            }
        }
        $invoiceId = $report->reportInvoice()->value('invoice_id');
        $invoiceStatus = Invoice::whereId($invoiceId)->value('status');
        $duration = array_sum(Arr::pluck($reports, 'duration'));
        $totalHours = $this->reportRepository->getDurationTime($duration);
        $data = [
            'report' => $report,
            'reports' => $reports,
            'totalHours' => $totalHours,
            'totalMinutes' => $duration,
        ];

        return view('reports.show', compact('invoiceId', 'invoiceStatus'))->with($data);
    }

    /**
     * Show the form for editing the specified Report.
     *
     * @param  Report  $report
     * @return RedirectResponse
     */
    public function edit(Report $report)
    {
        $user = getLoggedInUser();
        if (! $user->hasPermissionTo('manage_reports') || ! $user->hasRole('Admin')) {
            if ($report->owner_id != $user->id) {
                return redirect()->back();
            }
        }
        $id = $report->id;
        $data['report'] = $report;
        $data['projectIds'] = $this->reportRepository->getProjectIds($id);
        $data['tagIds'] = $this->reportRepository->getTagIds($id);
        $data['userIds'] = $this->reportRepository->getUserIds($id);
        $data['clientId'] = $this->reportRepository->getClientId($id);
        $data['departmentId'] = $this->reportRepository->getDepartmentId($id);
        $data['projects'] = $this->projectRepo->getProjectsByClients($data['clientId']);
        $data['users'] = $this->reportRepository->getUserList($data['projectIds']);
        $data['clients'] = $this->clientRepo->getClientsByDepartments($data['departmentId']);
        $data['tags'] = $this->tagRepo->getTagList();
        $data['departments'] = $this->departmentRepo->getDepartmentList();

        return view('reports.edit')->with($data);
    }

    /**
     * Update the specified Report in storage.
     *
     * @param  Report  $report
     * @param  UpdateReportRequest  $request
     * @return RedirectResponse|Redirector
     *
     * @throws Exception
     */
    public function update(Report $report, UpdateReportRequest $request)
    {
        $input = $request->all();

        $this->reportRepository->update($input, $report->id);
        Flash::success('Report updated successfully.');

        return redirect(route('reports.show', $report));
    }

    /**
     * Remove the specified Report from storage.
     *
     * @param  Report  $report
     * @param  Request  $request
     * @return JsonResponse|RedirectResponse|Redirector
     *
     * @throws Exception
     */
    public function destroy(Report $report, Request $request)
    {
        $user = getLoggedInUser();
        if (! $user->hasPermissionTo('manage_reports') || ! $user->hasRole('Admin')) {
            if ($report->owner_id != $user->id) {
                return $this->sendError('Seems, you are not allowed to access this record.');
            }
        }

        $invoiceExist = $report->reportInvoice()->exists();
        if ($invoiceExist) {
            return $this->sendError('Report can\'t be deleted.');
        }

        $this->reportRepository->delete($report->id);

        if ($request->ajax()) {
            return $this->sendSuccess('Report deleted successfully.');
        }

        return redirect(route('reports.index'));
    }

    /**
     * @param  CreateReportRequest  $request
     * @return JsonResponse
     *
     * @throws Throwable
     */
    public function showPreview(CreateReportRequest $request)
    {
        $input = $request->all();
        $input['owner_id'] = Auth::id();
        $report = new Report($input);
        $reports = $this->reportRepository->getReport($report, $input);

        $duration = array_sum(Arr::pluck($reports, 'duration'));
        $totalHours = $this->reportRepository->getDurationTime($duration);
        $data = [
            'report' => $report,
            'reports' => $reports,
            'totalHours' => $totalHours,
            'totalMinutes' => $duration,
        ];

        $view = view('reports.preview', $data)->render();

        return $this->sendResponse($view, 'Preview retrieved successfully.');
    }

    public function projectUsers(Request $request)
    {
        $projectIds = $request->get('projectIds', null);

        $projectIds = (! is_null($projectIds)) ? explode(',', $projectIds) : [];
        $users = $this->reportRepository->getUserList($projectIds);

        return $this->sendResponse($users, 'Users Retrieved successfully.');
    }
}
