<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateStatusRequest;
use App\Http\Requests\UpdateStatusRequest;
use App\Models\Setting;
use App\Models\Status;
use App\Repositories\StatusRepository;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StatusController extends AppBaseController
{
    /** @var StatusRepository */
    private $statusRepository;

    /**
     * TagController constructor.
     *
     * @param  StatusRepository  $statusRepo
     */
    public function __construct(StatusRepository $statusRepo)
    {
        $this->statusRepository = $statusRepo;
    }

    /**
     * @param  Request  $request
     * @return Factory|View
     */
    public function index(Request $request)
    {
        return view('status.index');
    }

    /**
     * @return JsonResponse
     */
    public function orderNumber()
    {
        $latest_status = Status::orderBy('order', 'desc')->first();

        return $this->sendResponse($latest_status, 'Status created successfully.');
    }

    /**
     * Store a newly created Status in storage.
     *
     * @param  CreateStatusRequest  $request
     * @return JsonResponse
     */
    public function store(CreateStatusRequest $request)
    {
        $input = $request->all();
        $data['status'] = $this->statusRepository->store($input);

        $data['statuses'] = Status::orderBy('name', 'asc')->pluck('status', 'name')->toArray();

        return $this->sendResponse($data, 'Status created successfully.');
    }

    /**
     * Show the form for editing the specified Status.
     *
     * @param  Status  $status
     * @return JsonResponse
     */
    public function edit(Status $status)
    {
        return $this->sendResponse($status, 'Status retrieved successfully.');
    }

    /**
     * Update the specified Status in storage.
     *
     * @param  Status  $status
     * @param  UpdateStatusRequest  $request
     * @return JsonResponse
     */
    public function update(Status $status, UpdateStatusRequest $request)
    {
        $this->statusRepository->update($request->all(), $status->id);

        return $this->sendSuccess('Status updated successfully.');
    }

    /**
     * Remove the specified Status from storage.
     *
     * @param  Status  $status
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(Status $status)
    {
        $defaultTaskStatus = Setting::where('key', 'default_task_status')->first()->value;
        if ($status->status == $defaultTaskStatus) {
            return $this->sendError('Status is default can not be deleted.');
        }
        $status = $this->statusRepository->deleteStatus($status->id);

        if ($status) {
            return $this->sendSuccess('Status deleted successfully.');
        }

        return $this->sendError('Status can not be deleted.');
    }
}
