<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\AppBaseController;
use App\Models\Department;
use App\Models\Project;
use App\Models\ProjectActivity;
use App\Models\Task;
use App\Repositories\ClientRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\TaskRepository;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class ProjectController extends AppBaseController
{
    /** @var ProjectRepository */
    private $invoiceRepository;
    private $userRepository;

    public function __construct(ProjectRepository $invoiceRepo, UserRepository $userRepository)
    {
        $this->invoiceRepository = $invoiceRepo;
        $this->userRepository = $userRepository;
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function index(Request $request)
    {
        return view('client_panel.projects.index');
    }

    /**
     * @param  Project  $project
     * @return Application|Factory|View
     */
    public function show(Project $project, ClientRepository $clientRepository)
    {
        $projectIds = Project::whereClientId(getLoggedInUser()->owner_id)->pluck('id')->toArray();
        if (!in_array($project->id, $projectIds)) {
            return redirect()->back();
        }
        $project = Project::with('users.media')->findOrFail($project->id);
        $completedTasks = $project->tasks->where('status', '=', Task::STATUS_COMPLETED)->count();
        $activities = ProjectActivity::with('createdBy')->where('subject_id', '=',
            $project->id)->orderByDesc('created_at')->get();

//        $clients = $clientRepository->getClientList();
//        $users = $this->userRepository->getUserList();
//        $currencies = Project::CURRENCY;
//        $budgetTypes = Project::BUDGET_TYPE;
//        $projectStatus = Arr::except(Project::STATUS, Project::STATUS_All);
//        $openTasks = $project->openTasks->count();
//        $project = Project::with('createdUser', 'users.media')->findOrFail($project->id);
//        $data = $this->invoiceRepository->getProjectsDetails($project);
        $taskRepo = app(TaskRepository::class);
        $taskData = $taskRepo->getTaskData();
        $priority = $taskData['priority'];
        $tags = $taskData['tags'];
        $projects = Project::where('client_id', getLoggedInUser()->owner_id)->pluck('name', 'id')->toArray();
        $activities = ProjectActivity::with('createdBy')->where('subject_id', '=',
            $project->id)->where('subject_type', '=', Project::class)->orderByDesc('created_at')->get();
        $departments = Department::orderBy('name', 'asc')->pluck('name', 'id')->toArray();

        return view('client_panel.projects.show',
            compact('project', 'completedTasks', 'activities', 'project',
//                'clients', 'users', 'currencies',
//                'budgetTypes', 'projectStatus', 'openTasks',
                'projects', 'activities', 'priority', 'tags',
//                'data', 
                'departments', 'taskData'));
    }
}
