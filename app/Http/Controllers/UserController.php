<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserProfileRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Project;
use App\Models\ProjectActivity;
use App\Models\Task;
use App\Models\TimeEntry;
use App\Models\User;
use App\Queries\UserDataTable;
use App\Repositories\ProjectRepository;
use App\Repositories\ReportRepository;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use DataTables;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * Class UserController.
 */
class UserController extends AppBaseController
{
    /** @var UserRepository */
    private $userRepository;

    /** @var ReportRepository */
    private $reportRepository;

    /** @var ReportRepository */
    private $projectRepository;

    /**
     * UserController constructor.
     *
     * @param  UserRepository  $userRepo
     * @param  ReportRepository  $reportRepo
     * @param  ProjectRepository  $projectRepo
     */
    public function __construct(
        UserRepository $userRepo,
        ReportRepository $reportRepo,
        ProjectRepository $projectRepo
    ) {
        $this->userRepository = $userRepo;
        $this->reportRepository = $reportRepo;
        $this->projectRepository = $projectRepo;
    }

    /**
     * Display a listing of the User.
     *
     * @param  Request  $request
     * @param  RoleRepository  $roleRepository
     * @param  ProjectRepository  $projectRepository
     * @return Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request, RoleRepository $roleRepository, ProjectRepository $projectRepository)
    {
        if ($request->ajax()) {
            return Datatables::of((new UserDataTable())->get())->addColumn('role_name', function (User $user) {
                return implode(',', $user->roles()->pluck('name')->toArray());
            })->make(true);
        }

        $projects = $projectRepository->getProjectsList();
        $roles = $roleRepository->getRolesList();
        $status = User::STATUS;

        return view('users.index')->with(['projects' => $projects, 'roles' => $roles, 'status' => $status]);
    }

    /**
     * Store a newly created User in storage.
     *
     * @param  CreateUserRequest  $request
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function store(CreateUserRequest $request)
    {
        $input = $request->all();
        $input['salary'] = (! empty($input['salary']) ? removeCommaFromNumbers($input['salary']) : null);

        $this->userRepository->store($input);

        return $this->sendSuccess('User created successfully.');
    }

    /**
     * @param  User  $user
     * @param  RoleRepository  $roleRepository
     * @param  ProjectRepository  $projectRepository
     * @return Application|Factory|View
     */
    public function show(User $user, RoleRepository $roleRepository, ProjectRepository $projectRepository)
    {
        $user = User::with('media', 'projects', 'client', 'userActiveTask')->findOrFail($user->id);

        $projects = $projectRepository->getProjectsList();
        $roles = $roleRepository->getRolesList();

        return view('users.show', compact('user', 'projects', 'roles'));
    }

    /**
     * Show the form for editing the specified User.
     *
     * @param  User  $user
     * @return JsonResponse
     */
    public function edit(User $user)
    {
        $userObj = $user->toArray();
        $userObj['project_ids'] = $user->projects()->pluck('project_id')->toArray();
        $userObj['role_id'] = $user->roles()->pluck('role_id')->toArray();

        return $this->sendResponse($userObj, 'User retrieved successfully.');
    }

    /**
     * Update the specified User in storage.
     *
     * @param  User  $user
     * @param  UpdateUserRequest  $request
     * @return JsonResponse|RedirectResponse
     *
     * @throws Exception
     */
    public function update(User $user, UpdateUserRequest $request)
    {
        $input = $request->all();
        $input['is_active'] = isset($input['is_active']) ? 1 : 0;
        $input['email_verified_at'] = isset($input['email_verified_at']) ? Carbon::now() : null;
        if ($user->id == getLoggedInUserId() && ! $input['is_active']) {
            return $this->sendError('Login user can\'t De-active itself.', 404);
        }
        if (! getLoggedInUser()->hasRole('Admin')) {
            if (getLoggedInUserId() == $user->id) {
                return $this->sendError('Login user can\'t change role itself.', 404);
            }
        }
        $input['salary'] = (! empty($input['salary']) ? removeCommaFromNumbers($input['salary']) : null);

        $this->userRepository->update($input, $user->id);

        return $this->sendSuccess('User updated successfully.');
    }

    /**
     * Remove the specified User from storage.
     *
     * @param  User  $id
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $user = User::find($id);
            if ($user->id == getLoggedInUserId()) {
                return $this->sendError('Login user can\'t deleted');
            }
            $deleteTimeEntry = $this->userRepository->deleteTimeEntry($user->id);

            $projectIds = Project::where('created_by', '=', $user->id)->pluck('id');
            if (! empty($projectIds->toArray())) {
                foreach ($projectIds as $projectId) {
                    $this->projectRepository->delete($projectId);
                }
            } else {
                $reports = $user->reports;
                foreach ($reports as $report) {
                    $this->reportRepository->deleteFilter($report->id);
                    $invoice = $report->reportInvoice()->delete();
                    $report->delete();
                }
                $taskIds = Task::where('created_by', '=', $user->id)->pluck('id')->toArray();
                $timeEntryIds = TimeEntry::whereIn('task_id', $taskIds)->pluck('id')->toArray();
                if (! empty($timeEntryIds)) {
                    foreach ($timeEntryIds as $timeEntryId) {
                        $timeEntry = TimeEntry::find($timeEntryId);
                        $timeEntry->update(['deleted_by' => getLoggedInUserId()]);
                        $timeEntry->delete();
                    }
                }
                foreach ($taskIds as $taskId) {
                    $task = Task::find($taskId);
                    $task->update(['deleted_by' => getLoggedInUserId()]);
                    $task->comments()->delete();
                    $task->taskAssignee()->detach();
                    $task->delete();
                    $task->tags()->detach();
                }
            }

            ProjectActivity::whereCauserId($user->id)->where('causer_type', User::class)->delete();
            $user->deleted_by = getLoggedInUserId();
            $user->comments()->delete();
            $user->save();
            $modelHasRole = DB::table('model_has_roles')->where('model_type', '=', \App\Models\User::class)->where('model_id', '=', $user->id)->delete();
            $user->delete();

            DB::commit();

            return $this->sendSuccess('User deleted successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return $this->sendError($e->getMessage());
        }
    }

    /**
     * @param  User  $user
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function resendEmailVerification(User $user): JsonResponse
    {
        $user->sendEmailVerificationNotification();

        return $this->sendSuccess('Verification email has been sent successfully.');
    }

    /**
     * @param  UpdateUserProfileRequest  $request
     * @return JsonResponse
     */
    public function profileUpdate(UpdateUserProfileRequest $request): JsonResponse
    {
        $input = $request->all();

        $this->userRepository->profileUpdate($input);

        return $this->sendSuccess('Profile updated successfully.');
    }

    /**
     * @param  ChangePasswordRequest  $request
     * @return JsonResponse
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var User $user */
        $user = Auth::user();

        if (! Hash::check($input['password_current'], $user->password)) {
            return $this->sendError('Current password is invalid.');
        }

        $input['password'] = Hash::make($input['password']);
        $user->update($input);

        return $this->sendSuccess('Password updated successfully.');
    }

    /**
     * @param  User  $user
     * @return JsonResponse
     */
    public function activeDeActiveUser(User $user): JsonResponse
    {
        if ($user->id == getLoggedInUserId()) {
            return $this->sendError('Login user can\'t De-active itself.', 404);
        }
        $this->userRepository->activeDeActiveUser($user->id);

        return $this->sendSuccess('User updated successfully.');
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function updateLanguage(Request $request): JsonResponse
    {
        $language = $request->get('languageName');

        $user = getLoggedInUser();
        $user->update(['language' => $language]);

        return $this->sendSuccess('Language updated successfully.');
    }

    /**
     * @return JsonResponse
     */
    public function getUserLists(): JsonResponse
    {
        $data = [];
        $users = User::where('email_verified_at', '!=',
            null)->whereOwnerId(null)->whereOwnerType(null)->whereIsActive(true)->orderBy('name')->pluck('name', 'id');

        $endTime = TimeEntry::whereUserId(\Auth::id())->whereDate('created_at', Carbon::now())->orderByDesc('created_at')->first();
        $data['endTime'] = is_null($endTime) ? null : $endTime->end_time;
        $data['users'] = $users;

        return $this->sendResponse($data, 'User retrieved successfully.');
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function restoreUser($id): JsonResponse
    {
        $user = User::withTrashed()->where('id', $id)->restore();
        if ($user) {
            $getUser = User::whereId($id)->first();
            $getUser->update([
                'is_active' => User::DEACTIVE,
            ]);
        }

        return $this->sendResponse($user, 'User restore successfully.');
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function UserDelete($id): JsonResponse
    {
        try {
            DB::beginTransaction();
            $user = User::withTrashed()->whereId($id)->first();
            \Schema::disableForeignKeyConstraints();
            $user->projects()->detach();
            $user->timeEntries()->forceDelete();
            $user->comments()->forceDelete();
            $user->tasks()->forceDelete();
            $user->reports()->forceDelete();
            $user->expenses()->forceDelete();
            $user->usersProjects()->forceDelete();
            $user->forceDelete();
            \Schema::enableForeignKeyConstraints();
            DB::commit();

            return $this->sendSuccess('User deleted successfully.');
        } catch (Exception $e) {
            DB::rollBack();

            return $this->sendError($e->getMessage());
        }
    }
}
