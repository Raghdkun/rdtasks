<?php

namespace App\Repositories;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Report;
use App\Models\ReportFilter;
use App\Models\Role;
use App\Models\Task;
use App\Models\TimeEntry;
use App\Models\User;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * Class ClientRepository.
 */
class ClientRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'email',
        'website',
    ];

    /**
     * Return searchable fields.
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model.
     **/
    public function model()
    {
        return Client::class;
    }

    /**
     * get clients.
     *
     * @param  null  $departmentId
     * @return Collection
     */
    public function getClientList($departmentId = null)
    {
        $query = Client::toBase()->orderBy('name', 'asc');

        if (! empty($departmentId)) {
            $query->where('department_id', '=', $departmentId);

            return $query->pluck('id', 'name');
        }

        return $query->pluck('name', 'id');
    }

    /**
     * @param  int  $clientId
     * @return bool|mixed|void|null
     *
     * @throws Exception
     */
    public function delete($clientId)
    {
        try {
            DB::beginTransaction();

            /** @var Client $client */
            $client = $this->find($clientId);

            $reports = ReportFilter::where('param_id', '=', $clientId)->where('param_type', '=', Client::class)->pluck('report_id');
            foreach ($reports as $reportId) {
                $report = Report::find($reportId);
                $clients = $report->meta['all_clients'];
                if (! empty($reports) && $clients == false) {
                    $report->reportInvoice()->detach();
                    $report->update(['invoice_generate' => 0]);
                    $report->delete();
                    ReportFilter::ofReport($reportId)->delete();
                }
            }
            $projectIds = Project::where('client_id', '=', $client->id)->get()->pluck('id');
            $taskIds = Task::whereIn('project_id', $projectIds)->get()->pluck('id');
            $userID = User::where('owner_id', '=', $client->id)->get()->pluck('id');
            if (count($projectIds) != 0) {
                foreach ($projectIds as $projectId) {
                    $reportFilterProjectId = ReportFilter::where('param_id', $projectId)->where('param_type', '=', Project::class);
                    $reportFilterProjectId->delete();
                }
            }
            $reportFilterClientId = ReportFilter::where('param_id', '=', $clientId)->where('param_type', '=', Client::class);
            $reportFilterClientId->delete();

            TimeEntry::whereIn('task_id', $taskIds)->update(['deleted_by' => getLoggedInUserId()]);
            TimeEntry::whereIn('task_id', $taskIds)->delete();

            $taskIds = Task::whereIn('project_id', $projectIds)->pluck('id');
            foreach ($taskIds as $taskId) {
                $task = Task::find($taskId);
                $task->update(['deleted_by' => getLoggedInUserId()]);
                $task->delete();
                $task->tags()->detach();
            }

            User::whereIn('id', $userID)->update(['deleted_by' => getLoggedInUserId()]);
            User::whereIn('id', $userID)->delete();

            $client->expenses()->update(['deleted_by' => getLoggedInUserId()]);
            $client->expenses()->delete();

            $client->projects()->update(['deleted_by' => getLoggedInUserId()]);
            $client->projects()->delete();

            $client->update(['deleted_by' => getLoggedInUserId()]);
            $client->delete();

            //invoice delete
            $invoiceClient = DB::table('invoice_clients')->where('client_id', $clientId)->first();
            if (! empty($invoiceClient)) {
                $invoice = Invoice::find($invoiceClient->invoice_id);
                if (! empty($invoice)) {
                    $invoice->invoiceClients()->detach();     //delete invoice Clients
                    $invoice->invoiceProjects()->detach();    //delete invoice Projects.
                    $invoice->invoiceItems()->delete();       //delete invoice Items.

                    $invoice->delete();
                }
            }
            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            \Log::error($e->getMessage());
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    /**
     * @param  array  $input
     * @return void
     */
    public function store($input)
    {
        $input['password'] = is_null($input['password']) ? '' : Hash::make($input['password']);
        $input['email'] = is_null($input['email']) ? '' : $input['email'];
        $input['website'] = is_null($input['website']) ? '' : $input['website'];
        try {
            $emailExist = User::where('email', $input['email'])->exists();

            if ($emailExist) {
                throw new UnprocessableEntityHttpException('Email address already exist.');
            }
            if (empty($input['email']) && ! empty($input['password'])) {
                throw new UnprocessableEntityHttpException('Please Enter Email.');
            }

            $client = Client::create($input);

            if (! empty($input['email']) && ! empty($input['password'])) {

                /** @var User $user */
                $user = User::create($input);

                $client->update(['user_id' => $user->id]);

                $ownerId = $client->id;
                $ownerType = Client::class;

                $user->email_verified_at = Carbon::now();
                $user->is_email_verified = 1;
                $user->is_active = 1;
                $user->set_password = true;

                $user->update(['owner_id' => $ownerId, 'owner_type' => $ownerType]);
                $roleId = Role::whereDisplayName('Client')->first('id');
                $user->roles()->sync($roleId);
            }
            activity()
                ->causedBy(getLoggedInUser())
                ->withProperties(['modal' => Client::class, 'data' => ''])
                ->performedOn($client)
                ->useLog('New Client created.')
                ->log('New Client '.$client->name.' created.');
        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
        if (! empty($input['photo'])) {
            $client->addMedia($input['photo'])->toMediaCollection(Client::IMAGE_PATH, config('app.media_disc'));
        }

        return $client;
    }

    /**
     * @param  array  $input
     * @param  int  $id
     * @return void
     */
    public function update($input, $id)
    {
        $input['email'] = is_null($input['email']) ? '' : $input['email'];
        $input['website'] = is_null($input['website']) ? '' : $input['website'];
        $input['password'] = is_null($input['password']) ? '' : Hash::make($input['password']);
        try {
            $userByEmail = User::whereEmail($input['email'])->first();
            $userEmail = ! empty($userByEmail) ? $userByEmail->email : '';
            $userOwnerId = ! empty($userByEmail) ? $userByEmail->owner_id : '';

            $user = User::whereOwnerId($id)->first();
            $emailExistClient = Client::whereEmail($input['email'])->exists();
            $client = Client::findOrFail($id);

            if ($emailExistClient && $userEmail == $input['email'] && ! empty($input['email']) || ! empty($user)) {
//                if ($user->owner_id != $client->id || $userOwnerId != $client->id && ! empty($userByEmail)) {
//                    throw new UnprocessableEntityHttpException('Email address already exists.');
//                }
                if (empty($input['email'])) {
                    $this->delete($id);
                } else {
                    $client->update($input);

                    $user = User::where('email', '=', $input['email'])->first();
                    $user->update([
                        'email' => $input['email'],
                        'password' => empty($input['password']) ? $user->password : $input['password'],
                        'name' => $input['name'],
                        'email_verified_at' => Carbon::now(),
                        'is_email_verified' => 1,
                        'is_active' => 1,
                    ]);
                }
            } else {
                if (! empty($input['email']) && ! empty($input['password']) && empty($user)) {
                    $emailExist = User::where('email', $input['email'])->exists();
                    if ($emailExist) {
                        throw new UnprocessableEntityHttpException('Email address already exist.');
                    }
                    $client->update($input);

                    /** @var User $user */
                    $user = User::create($input);

                    $client->update(['user_id' => $user->id]);

                    $ownerId = $client->id;
                    $ownerType = Client::class;

                    $user->email_verified_at = Carbon::now();
                    $user->is_email_verified = 1;
                    $user->is_active = 1;
                    $user->set_password = true;

                    $user->update(['owner_id' => $ownerId, 'owner_type' => $ownerType]);
                    $roleId = Role::whereDisplayName('Client')->first('id');
                    $user->roles()->sync($roleId);
                } elseif (empty($input['email']) && ! empty($input['password'])) {
                    throw new UnprocessableEntityHttpException('Please enter valid email.');
                } elseif ((! empty($input['email']) && empty($user)) || empty($input['email'])) {
                    $client->update($input);
                } else {
                    throw new UnprocessableEntityHttpException('Email address already exist.');
                }
            }
        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }

        if (! empty($input['photo'])) {
            $client->clearMediaCollection(Client::IMAGE_PATH);
            $client->addMedia($input['photo'])->toMediaCollection(Client::IMAGE_PATH, config('app.media_disc'));
        }
    }

    /**
     * @param $departmentIds
     * @return mixed
     */
    public function getClientsByDepartments($departmentIds)
    {
        $query = Client::orderBy('name');

        if (count($departmentIds) > 0) {
            $query->whereIn('department_id', $departmentIds);
        }

        return $query->pluck('name', 'id');
    }

    /**
     * @param  array  $input
     * @return bool
     */
    public function profileUpdate($input)
    {
        /** @var User $user */
        $client = Client::findOrfail($input['client_id']);
        try {
            if (isset($input['photo']) && ! empty($input['photo'])) {
                $client->clearMediaCollection(Client::IMAGE_PATH);
                $client->addMedia($input['photo'])->toMediaCollection(Client::IMAGE_PATH, config('app.media_disc'));
            }
            $client->update($input);
            $client->user->update($input);
        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }

        return true;
    }
}
