<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Department;
use App\Models\Project;
use App\Models\ProjectActivity;
use App\Models\Task;
use App\Models\User;
use App\Queries\ProjectDataTable;
use App\Repositories\ClientRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\TaskRepository;
use App\Repositories\UserRepository;
use DataTables;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Class ProjectController.
 */
class ProjectController extends AppBaseController
{
    /** @var ProjectRepository */
    private $projectRepository;

    /** @var UserRepository */
    private $userRepository;

    /**
     * ProjectController constructor.
     *
     * @param  ProjectRepository  $projectRepo
     * @param  UserRepository  $userRepository
     */
    public function __construct(
        ProjectRepository $projectRepo,
        UserRepository $userRepository
    ) {
        $this->projectRepository = $projectRepo;
        $this->userRepository = $userRepository;
    }

    /**
     * Display a listing of the Project.
     *
     * @param  Request  $request
     * @param  ClientRepository  $clientRepository
     * @return Application|Factory|\Illuminate\Contracts\View\View|View
     *
     * @throws Exception
     */
    public function index(Request $request, ClientRepository $clientRepository)
    {
        if ($request->ajax()) {
            return Datatables::of(
                (new ProjectDataTable())->get(
                    $request->only('filter_client')
                )
            )->make(true);
        }

        $clients = $clientRepository->getClientList();
        $users = $this->userRepository->getUserList();
        $currencies = Project::CURRENCY;
        $budgetTypes = Project::BUDGET_TYPE;
        $projectStatus = Arr::except(Project::STATUS, Project::STATUS_All);
        $departments = Department::toBase()->orderBy('name', 'asc')->pluck('name', 'id')->toArray();
        if (Auth::user()->hasPermissionTo('manage_projects')) {
            return view('projects.index', compact('clients', 'users', 'departments', 'currencies', 'budgetTypes', 'projectStatus'));
        }

        return view('my_projects.index', compact('users'));
    }

    /**
     * Store a newly created Project in storage.
     *
     * @param  CreateProjectRequest  $request
     * @return JsonResponse
     */
    public function store(CreateProjectRequest $request)
    {
        $input = $request->all();

        $this->projectRepository->store($input);

        return $this->sendSuccess('Project created successfully.');
    }

    /**
     * @param  Project  $project
     * @param  ClientRepository  $clientRepository
     * @return Application|Factory|View
     */
    public function show(Project $project, ClientRepository $clientRepository)
    {
        $clients = $clientRepository->getClientList();
        $users = $this->userRepository->getUserList();
        $currencies = Project::CURRENCY;
        $budgetTypes = Project::BUDGET_TYPE;
        $projectStatus = Arr::except(Project::STATUS, Project::STATUS_All);
        $openTasks = $project->openTasks->count();
        $project = Project::with('createdUser', 'users.media')->findOrFail($project->id);
        $data = $this->projectRepository->getProjectsDetails($project);
        $taskRepo = app(TaskRepository::class);
        $taskData = $taskRepo->getTaskData();
        $activities = ProjectActivity::with('createdBy')->where('subject_id', '=',
            $project->id)->where('subject_type', '=', Project::class)->orderByDesc('created_at')->get();
        $departments = Department::orderBy('name', 'asc')->pluck('name', 'id')->toArray();

        return view('projects.show',
            compact('project', 'clients', 'users', 'currencies', 'budgetTypes', 'projectStatus', 'openTasks',
                'activities',
                'data', 'departments'))->with($taskData);
    }

    /**
     * Show the form for editing the specified Project.
     *
     * @param  Project  $project
     * @return JsonResponse|RedirectResponse
     */
    public function edit(Project $project)
    {
        $users = $project->users->pluck('id')->toArray();
        $allUsers = $this->userRepository->getUserList();

        return $this->sendResponse(['project' => $project, 'users' => $users, 'allUsers' => $allUsers], 'Project retrieved successfully.');
    }

    /**
     * Update the specified Client in storage.
     *
     * @param  Project  $project
     * @param  UpdateProjectRequest  $request
     * @return JsonResponse|RedirectResponse
     */
    public function update(Project $project, UpdateProjectRequest $request)
    {
        $input = $request->all();
        $input['price'] = (! empty($input['price']) ? removeCommaFromNumbers($input['price']) : null);
        if ($input['status'] == Project::STATUS_FINISHED) {
            if ($project->tasks()->where('status', '=', Task::$status['STATUS_ACTIVE'])->count() > 0) {
                return $this->sendError('This project has pending tasks.');
            }
        }

        $this->projectRepository->update($input, $project->id);

        return $this->sendSuccess('Project updated successfully.');
    }

    /**
     * Remove the specified Project from storage.
     *
     * @param  Project  $project
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(Project $project)
    {
        $this->projectRepository->delete($project->id);

        return $this->sendSuccess('Project deleted successfully.');
    }

    /**
     * @return JsonResponse
     */
    public function getMyProjects()
    {
        $projects = $this->projectRepository->getMyProjects();

        return $this->sendResponse($projects, 'Project Retrieved successfully.');
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function users(Request $request)
    {
        $projectIds = $request->get('projectIds', null);

        $projectIdsArr = (! is_null($projectIds)) ? explode(',', $projectIds) : [];
        $users = $this->userRepository->getUserList($projectIdsArr);

        return $this->sendResponse($users, 'Users Retrieved successfully.');
    }

    /**
     * @param  User  $user
     * @return JsonResponse
     */
    public function getProjectsByUser(User $user)
    {
        $projectList = $this->projectRepository->getProjectsByUserId($user->id);

        return $this->sendResponse($projectList, 'Projects Retrieved successfully.');
    }

    /**
     * @return Application|Factory|View
     */
    public function userAssignProjects()
    {
        $users = $this->userRepository->getUserList();

        return view('my_projects.index', compact('users'));
    }

    /**
     * @param  Project  $project
     * @return Application|Factory|RedirectResponse|View
     */
    public function userAssignProjectsShow(Project $project)
    {
        $projectIds = getLoggedInUser()->projects->pluck('id')->toArray();
        if (! in_array($project->id, $projectIds)) {
            return redirect()->back();
        }
        $project = Project::with('users.media')->findOrFail($project->id);
        $data = $this->projectRepository->getAssignProjectDetail($project);
        $taskRepo = app(TaskRepository::class);
        $taskData = $taskRepo->getTaskData();

        return view('my_projects.show', compact('project', 'data'))->with($taskData);
    }

    /**
     * @return JsonResponse
     */
    public function getLoginUsersProjects(): JsonResponse
    {
        $projects = getLoggedInUser()->projects()->where('status', '!=',
            Project::STATUS_FINISHED)->orderBy('name')->get()->pluck('id', 'name')->toArray();

        if (getLoggedInUser()->can('manage_projects')) {
            $projects = Project::where('status', '!=', Project::STATUS_FINISHED)->orderBy('name')->pluck('id',
                'name')->toArray();
        }

        return $this->sendResponse($projects, 'Projects Retrieved successfully.');
    }

    /**
     * @param Project $project
     * @param Request $request
     * @return JsonResponse
     */
    public function addAttachment(Project $project, Request $request)
    {
        $input = $request->all();

        if (isset($project->media) && $project->media->count() >= 25) {

            return $this->sendError('You can not upload more than 25 files', 422);
        }

        try {
            $result = $this->projectRepository->uploadFile($project, $input['file']);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), $e->getCode());
        }

        return $this->sendResponse($result, 'File has been uploaded successfully.');

    }

    /**
     * @param Project $project
     * @return JsonResponse
     */
    public function getAttachment(Project $project)
    {
        $result = $this->projectRepository->getAttachments($project->id);

        return $this->sendResponse($result, 'Attachment retrieved successfully.');
    }


    /**
     * @param $id
     * @return mixed
     */
    public function downloadAttachment(Request $request, $id)
    {
        if (!empty($request->task_id) && !getLoggedInUser()->can('manage_projects')) {
//            $projects = getLoggedInUser()->projects()->get()->pluck('id')->toArray();
            $task = Project::where('id', $request->project_id, function (Builder $q) {
                $q->where('user_id', getLoggedInUserId());
            })->whereHas('media', function (Builder $q) use ($id) {
                $q->where('id', $id);
            })->first();

            if (empty($task)) {
                \Flash::error('Seems, you are not allowed to access this record.');

                return redirect()->back();
            }
        }

        $media = Media::findOrFail($id);

        return $media;
    }


    /**
     * @param Media $media
     * @return JsonResponse
     */
    public function deleteAttachment(Media $media, Request $request)
    {
        if (!empty($request->project_id) && !getLoggedInUser()->can('manage_projects')) {
            $projects = getLoggedInUser()->projects()->get()->pluck('id')->toArray();

            $task = Project::where('id', $request->project_id, function (Builder $q) {
                $q->where('user_id', getLoggedInUserId());
            })->whereHas('media', function (Builder $q) use ($media) {
                $q->where('id', $media->id);
            })->first();

            if (empty($task)) {
                return $this->sendError('Seems, you are not allowed to access this record.');
            }
        }

        $media->delete();

        return $this->sendSuccess('File has been deleted successfully.');
    }
}
