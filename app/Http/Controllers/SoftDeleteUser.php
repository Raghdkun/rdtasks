<?php

namespace App\Http\Controllers;


use App\Http\Livewire\UserProjects;
use App\Models\ActivityType;
use App\Models\Client;
use App\Models\Comment;
use App\Models\Project;
use App\Models\Tag;
use App\Models\Task;
use App\Models\TimeEntry;
use App\Models\User;
use App\Queries\SoftDeleteUserDatatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class SoftDeleteUser extends AppBaseController
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return Datatables::of((new SoftDeleteUserDatatable())->get())->make(true);
        }

        return view('soft_delete_users.index');
    }

    public function destroy($user): JsonResponse
    {
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            DB::beginTransaction();
            $user = User::whereId($user)->onlyTrashed()->first();
            $tags = Tag::where('created_by', $user->id)->withTrashed()->get();
            foreach ($tags as $tag) {
                $tag->taskTags()->withTrashed()->detach();
            }
            Tag::where('created_by', $user->id)->withTrashed()->forceDelete();

            $tasks = Task::whereCreatedBy($user->id)->withTrashed()->get();
            TimeEntry::whereIn('task_id', $tasks->pluck('id')->toArray())->withTrashed()->forceDelete();
            $projects = Project::whereCreatedBy($user->id)->withTrashed()->get();
            $projectClient = Project::whereClientId($user->client()->withTrashed()->pluck('id')->toArray())->withTrashed()->get();
            foreach ($projects as $project) {
                $project->users()->detach();
            }
            DB::table('task_assignees')->whereIn('task_id',
                Task::whereCreatedBy($user->id)->withTrashed()->get()->pluck('id')->toArray())
                ->delete();
            DB::table('task_assignees')->whereIn('task_id',
                Task::whereIn('project_id', $projects->pluck('id')->toArray())->withTrashed()->get()->pluck('id')->toArray())
                ->delete();
            DB::table('task_assignees')->whereIn('task_id',
                Task::whereIn('project_id', $projectClient->pluck('id')->toArray())->withTrashed()->get()->pluck('id')->toArray())
                ->delete();
            TimeEntry::whereUserId($user->id)->delete();
            
            TimeEntry::whereIn('task_id', Task::whereCreatedBy($user->id)->withTrashed()->get()->pluck('id')->toArray())->withTrashed()->forceDelete();
            TimeEntry::whereIn('task_id', Task::whereIn('project_id', $projects->pluck('id')->toArray())->withTrashed()->get()->pluck('id')->toArray())->withTrashed()->forceDelete();
            TimeEntry::whereIn('task_id', Task::whereIn('project_id', $projectClient->pluck('id')->toArray())->withTrashed()->get()->pluck('id')->toArray())->withTrashed()->forceDelete();
            
            Comment::whereCreatedBy($user->id)->withTrashed()->forceDelete();
            Comment::whereIn('task_id', Task::whereCreatedBy($user->id)->withTrashed()->get()->pluck('id')->toArray())->withTrashed()->forceDelete();
            Comment::whereIn('task_id', Task::whereIn('project_id', $projects->pluck('id')->toArray())->withTrashed()->get()->pluck('id')->toArray())->withTrashed()->forceDelete();
            Comment::whereIn('task_id', Task::whereIn('project_id', $projectClient->pluck('id')->toArray())->withTrashed()->get()->pluck('id')->toArray())->withTrashed()->forceDelete();

            DB::table('task_assignees')->where('user_id', $user->id)->delete();
            DB::table('task_assignees')->whereIn('task_id', Task::whereCreatedBy($user->id)->withTrashed()->get()->pluck('id')->toArray())->delete();
            DB::table('task_assignees')->whereIn('task_id', Task::whereCreatedBy($user->id)->withTrashed()->pluck('id')->toArray())->delete();
            DB::table('task_assignees')->whereIn('task_id', Task::whereIn('project_id', Project::whereIn('client_id', Client::whereCreatedBy($user->id)->withTrashed()->get()->pluck('id')->toArray())->withTrashed()->get()->pluck('id'))->withTrashed()->pluck('id')->toArray())->delete();

            DB::table('task_tags')->whereIn('task_id', Task::whereCreatedBy($user->id)->withTrashed()->get()->pluck('id')->toArray())->delete();
            DB::table('task_tags')->whereIn('task_id', Task::whereCreatedBy($user->id)->withTrashed()->pluck('id')->toArray())->delete();
            DB::table('task_tags')->whereIn('task_id', Task::whereIn('project_id', Project::whereIn('client_id', Client::whereCreatedBy($user->id)->withTrashed()->get()->pluck('id')->toArray())->withTrashed()->get()->pluck('id'))->withTrashed()->pluck('id')->toArray())->delete();

            DB::table('time_entries')->whereIn('task_id', Task::whereCreatedBy($user->id)->withTrashed()->get()->pluck('id')->toArray())->delete();
            DB::table('time_entries')->whereIn('task_id', Task::whereCreatedBy($user->id)->withTrashed()->pluck('id')->toArray())->delete();
            DB::table('time_entries')->whereIn('task_id', Task::whereIn('project_id', Project::whereIn('client_id', Client::whereCreatedBy($user->id)->withTrashed()->get()->pluck('id')->toArray())->withTrashed()->get()->pluck('id'))->withTrashed()->pluck('id')->toArray())->delete();

            DB::table('comments')->whereIn('task_id', Task::whereCreatedBy($user->id)->withTrashed()->get()->pluck('id')->toArray())->delete();
            DB::table('comments')->whereIn('task_id', Task::whereCreatedBy($user->id)->withTrashed()->pluck('id')->toArray())->delete();
            DB::table('comments')->whereIn('task_id', Task::whereIn('project_id', Project::whereIn('client_id', Client::whereCreatedBy($user->id)->withTrashed()->get()->pluck('id')->toArray())->withTrashed()->get()->pluck('id'))->withTrashed()->pluck('id')->toArray())->delete();

            Task::whereCreatedBy($user->id)->withTrashed()->forceDelete();
            Task::whereIn('project_id', $projects->pluck('id')->toArray())->withTrashed()->forceDelete();
            Task::whereIn('project_id', Project::whereIn('client_id', Client::whereCreatedBy($user->id)->withTrashed()->get()->pluck('id')->toArray())->withTrashed()->get()->pluck('id'))->withTrashed()->forceDelete();
            DB::table('project_user')->whereIn('project_id',
                Project::whereCreatedBy($user->id)->withTrashed()->get()->pluck('id')->toArray())
                ->delete();
            DB::table('project_user')->whereIn('project_id',
                Project::whereIn('client_id', Client::whereCreatedBy($user->id)->withTrashed()->get()->pluck('id')->toArray())->withTrashed()->pluck('id')->toArray())
                ->delete();
            DB::table('expenses')->whereIn('project_id',
                Project::whereCreatedBy($user->id)->withTrashed()->get()->pluck('id')->toArray())
                ->delete();
            DB::table('expenses')->whereIn('project_id',
                Project::whereIn('client_id', Client::whereCreatedBy($user->id)->withTrashed()->get()->pluck('id')->toArray())->withTrashed()->pluck('id')->toArray())
                ->delete();
            DB::table('project_user')->where('user_id', $user->id)->delete();

            $users = User::whereCreatedBy($user->id)->withTrashed()->get();
            $userIds = $users->pluck('id')->toArray();
            $this->deleteCreatedUser($users, $userIds);
            $this->deleteDeletedUser($users, $userIds);

            DB::table('reports')->whereIn('owner_id', $userIds)->delete();
            DB::table('project_user')->whereIn('user_id', $userIds)->delete();
            DB::table('task_assignees')->whereIn('user_id', $userIds)->delete();
            DB::table('time_entries')->whereIn('user_id', $userIds)->delete();
            DB::table('users')->whereIn('id', $userIds)->delete();
            DB::table('users')->whereIn('id', $userIds)->delete();
            DB::table('project_user')
                ->whereIn('project_id', DB::table('projects')
                    ->whereIn('client_id', Client::whereIn('user_id',
                        $userIds)->withTrashed()->get()->pluck('id')->toArray())->pluck('id')->toArray())
                ->delete();
            DB::table('project_user')
                ->whereIn('project_id', DB::table('projects')
                    ->whereIn('client_id', Client::whereIn('created_by',
                        $userIds)->withTrashed()->get()->pluck('id')->toArray())->pluck('id')->toArray())
                ->delete();
            DB::table('projects')
                ->whereIn('client_id',
                    Client::whereIn('user_id', $userIds)->withTrashed()->get()->pluck('id')->toArray())
                ->delete();
            DB::table('projects')
                ->whereIn('client_id',
                    Client::whereIn('created_by', $userIds)->withTrashed()->get()->pluck('id')->toArray())
                ->delete();
            DB::table('project_user')
                ->whereIn('project_id',
                    Project::whereCreatedBy($user->id)->withTrashed()->get()->pluck('id')->toArray())
                ->delete();
            DB::table('project_user')
                ->whereIn('project_id', Project::whereIn('client_id',
                    Client::whereCreatedBy($user->id)->withTrashed()->get()->pluck('id')->toArray())->withTrashed()->get()->pluck('id')->toArray())->delete();
            DB::table('project_user')
                ->whereIn('project_id', Project::whereIn('client_id',
                    Client::whereUserId($user->id)->withTrashed()->get()->pluck('id')->toArray())->withTrashed()->get()->pluck('id')->toArray())->delete();
            Project::whereCreatedBy($user->id)->withTrashed()->forceDelete();
            Project::whereIn('client_id',
                Client::whereCreatedBy($user->id)->withTrashed()->get()->pluck('id')->toArray())->withTrashed()->forceDelete();
            Project::whereIn('client_id',
                Client::whereUserId($user->id)->withTrashed()->get()->pluck('id')->toArray())->withTrashed()->forceDelete();
            Project::whereIn('client_id',
                Client::whereUserId($user->id)->withTrashed()->get()->pluck('id')->toArray())->withTrashed()->forceDelete();
            $user->client()->forceDelete();
            Client::whereCreatedBy($user->id)->withTrashed()->forceDelete();
            ActivityType::whereCreatedBy($user->id)->withTrashed()->forceDelete();
            $user->deleteImage();
            $user->reports()->forceDelete();
            $user->timeEntries()->forceDelete();
            $user->activityLogs()->forceDelete();
            $user->comments()->forceDelete();
            $user->invoices()->forceDelete();
            $user->expenses()->forceDelete();
            $user->forceDelete();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            DB::commit();
        } catch (\Exception $exception) {
            DB::RollBack();
            return $this->sendError( $exception->getMessage());
        }

        return $this->sendSuccess('User deleted successfully.');
    }
    
    public function deleteCreatedUser($users, &$userIds)
    {
        foreach ($users as $user) {
            $usersData = DB::table('users')->where('created_by', $user->id)->get();
            $userIds += $usersData->pluck('id')->toArray();
            $userIds += $usersData->pluck('created_by')->toArray();
            $this->deleteCreatedUser($usersData, $userIds);
        }

    }

    public function deleteDeletedUser($users, &$userIds)
    {
        foreach ($users as $user) {
            $usersData = DB::table('users')->where('deleted_by', $user->id)->get();
            $userIds += $usersData->pluck('id')->toArray();
            $userIds += $usersData->pluck('deleted_by')->toArray();
            $this->deleteDeletedUser($usersData, $userIds);
        }

    }
}
