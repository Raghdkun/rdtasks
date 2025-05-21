<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateExpenseRequest;
use App\Models\Client;
use App\Models\Expense;
use App\Queries\ExpenseDatatable;
use App\Repositories\ClientRepository;
use App\Repositories\ExpenseRepository;
use App\Repositories\ProjectRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laracasts\Flash\Flash;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Yajra\DataTables\DataTables;

class ExpenseController extends AppBaseController
{
    /** @var ProjectRepository */
    private $projectRepository;

    /** @var ClientRepository */
    private $clientRepository;

    /**
     * @var ExpenseRepository */
    private $expenseRepository;

    public function __construct(
        ProjectRepository $projectRepo,
        ClientRepository $clientRepo,
        ExpenseRepository $expenseRepo
    ) {
        $this->projectRepository = $projectRepo;
        $this->clientRepository = $clientRepo;
        $this->expenseRepository = $expenseRepo;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  Request  $request
     * @return Response
     *
     * @throws Exception
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return Datatables::of((new ExpenseDatatable())->get())->make(true);
        }

        return view('expenses.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function create()
    {
        $category = Expense::CATEGORY;
        $clients = $this->clientRepository->getClientList();
        $projects = [];

        return view('expenses.create', compact('clients', 'projects', 'category'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateExpenseRequest  $request
     * @return JsonResponse
     */
    public function store(CreateExpenseRequest $request)
    {
        $input = $request->all();
        $input['amount'] = (! empty($input['amount']) ? removeCommaFromNumbers($input['amount']) : null);

        $this->expenseRepository->store($input);

        Flash::success('Expense added successfully.');

        return redirect(route('expenses.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return void
     */
    public function show($id)
    {
        $expense = Expense::find($id);

        return view('expenses.show', compact('expense'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Expense  $expense
     * @return void
     */
    public function edit(Expense $expense)
    {
        $expense = Expense::find($expense->id);
        $data['category'] = Expense::CATEGORY;
        $data['clients'] = $this->clientRepository->getClientList();
        $data['projects'] = $this->projectRepository->getProjectsList($expense->client_id);
        $data['amount'] = number_format($expense->amount, 2);

        return view('expenses.edit', compact('expense', 'data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  CreateExpenseRequest  $request
     * @param  int  $id
     * @return void
     */
    public function update(CreateExpenseRequest $request, $id)
    {
        $input = $request->all();
        $input['amount'] = (! empty($input['amount']) ? removeCommaFromNumbers($input['amount']) : null);

        $this->expenseRepository->update($input, $id);

        Flash::success('Expense updated successfully.');

        return redirect(route('expenses.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $expense = Expense::find($id);
        $expense->update(['deleted_by' => getLoggedInUserId()]);
        $expense->delete();

        return $this->sendSuccess('Expense deleted successfully.');
    }

    /**
     * @param  Client  $client
     * @return JsonResponse
     */
    public function getProjects(Client $client)
    {
        $projects = $this->projectRepository->getProjectsList($client->id);

        return $this->sendResponse($projects, 'Projects get successfully');
    }

    /**
     * @param  Media  $media
     * @return mixed
     */
    public function deleteAttachment(Media $media)
    {
        $media->delete();

        return $this->sendSuccess('File has been deleted successfully.');
    }

    /**
     * @param $id
     * @return mixed
     */
    public function downloadAttachment($id)
    {
        $media = Media::findOrFail($id);

        return $media;
    }
}
