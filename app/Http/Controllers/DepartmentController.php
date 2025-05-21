<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Models\Client;
use App\Models\Department;
use App\Queries\DepartmentDataTable;
use App\Repositories\ClientRepository;
use App\Repositories\DepartmentRepository;
use DataTables;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DepartmentController extends AppBaseController
{
    /** @var DepartmentRepository */
    private $departmentRepository;

    public function __construct(DepartmentRepository $departmentRepo)
    {
        $this->departmentRepository = $departmentRepo;
    }

    /**
     * Display a listing of the Department.
     *
     * @param  Request  $request
     * @return Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of((new DepartmentDataTable())->get())->make(true);
        }

        return view('departments.index');
    }

    /**
     * Store a newly created Department in storage.
     *
     * @param  CreateDepartmentRequest  $request
     * @return JsonResponse
     */
    public function store(CreateDepartmentRequest $request)
    {
        $input = $request->all();

        DB::beginTransaction();
        try {
            $data['department'] = $this->departmentRepository->create($input);
            activity()
                ->causedBy(getLoggedInUser())
                ->withProperties(['modal' => Department::class, 'data' => ''])
                ->performedOn($data['department'])
                ->useLog('New Department created.')
                ->log('New Department '.$data['department']->name.' created.');

            DB::commit();

            $data['departments'] = Department::orderBy('name', 'asc')->pluck('id', 'name')->toArray();

            return $this->sendResponse($data, 'Department created successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param  Department  $department
     * @return JsonResponse
     */
    public function show(Department $department)
    {
        return $this->sendResponse($department, 'Department Retrieved Successfully.');
    }

    /**
     * Show the form for editing the specified Department.
     *
     * @param  Department  $department
     * @return JsonResponse
     */
    public function edit(Department $department)
    {
        return $this->sendResponse($department, 'Department retrieved successfully.');
    }

    /**
     * Update the specified Department in storage.
     *
     * @param  Department  $department
     * @param  UpdateDepartmentRequest  $request
     * @return JsonResponse
     */
    public function update(Department $department, UpdateDepartmentRequest $request)
    {
        $this->departmentRepository->update($request->all(), $department->id);

        return $this->sendSuccess('Department updated successfully.');
    }

    /**
     * Remove the specified Department from storage.
     *
     * @param  Department  $department
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(Department $department)
    {
        $clientExists = Client::where('department_id', $department->id)->exists();
        $result = checkTimeEntryExist($department->id);
        if ($result || $clientExists) {
            return $this->sendError('Department can\'t be deleted.');
        }
        $this->departmentRepository->delete($department->id);

        return $this->sendSuccess('Department deleted successfully.');
    }

    /**
     * @param  Request  $request
     * @param  ClientRepository  $clientRepository
     * @return JsonResponse
     */
    public function clients(Request $request, ClientRepository $clientRepository)
    {
        $projects = $clientRepository->getClientList($request->get('department_id', null));

        return $this->sendResponse($projects, 'Clients retrieved successfully.');
    }
}
