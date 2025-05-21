<?php

namespace App\Repositories;

use App\Mail\SendUserPassword;
use App\Models\Project;
use App\Models\TimeEntry;
use App\Models\User;
use App\Models\UserNotification;
use Crypt;
use Exception;
use Hash;
use Illuminate\Container\Container as Application;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * Class UserRepository.
 *
 * @version May 2, 2019, 12:42 pm UTC
 */
class UserRepository extends BaseRepository
{
    private $accountRepo;

    public function __construct(Application $app, AccountRepository $accountRepo)
    {
        parent::__construct($app);
        $this->accountRepo = $accountRepo;
    }

    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'email',
        'phone',
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
        return User::class;
    }

    /**
     * @param  array  $projectIds
     * @return Collection
     */
    public function getUserList($projectIds = [])
    {
        /** @var Builder $query */
        $query = User::whereOwnerId(null)->whereOwnerType(null)->whereIsActive(true)->where('email_verified_at', '!=', null)->orderBy('name');
        if (! empty($projectIds)) {
            $query = $query->whereHas('projects', function (Builder $query) use ($projectIds) {
                $query->whereIn('projects.id', $projectIds);
            });
        }

        return $query->pluck('name', 'id');
    }

    /**
     * @param  array  $input
     * @return bool
     *
     * @throws Exception
     */
    public function setUserPassword($input)
    {
        $password = Hash::make($input['password']);
        /** @var User $user */
        $user = User::findOrFail($input['user_id']);

        $user->password = $password;
        $user->set_password = true;
        $user->save();

        Auth::login($user);

        return true;
    }

    /**
     * @param  int  $id
     * @return bool
     *
     * @throws Exception
     */
    public function resendEmailVerification($id)
    {
        /** @var AccountRepository $accountRepository */
        $accountRepository = new AccountRepository();
        $activation_code = uniqid();

        /** @var User $user */
        $user = $this->find($id);
        $user->activation_code = $activation_code;
        $user->save();

        $key = $user->id.'|'.$activation_code;
        $code = Crypt::encrypt($key);
        $accountRepository->sendConfirmEmail(
            $user->name,
            $user->email,
            $code
        );

        return true;
    }

    /**
     * @param  int  $id
     * @return User
     */
    public function activeDeActiveUser($id)
    {
        /** @var User $user */
        $user = $this->findOrFail($id);
        $user->is_active = ! $user->is_active;
        $user->save();

        return $user;
    }

    /**
     * @param  array  $input
     * @return true
     */
    public function profileUpdate($input)
    {
        /** @var User $user */
        $user = $this->findOrFail(Auth::id());
        try {
            if (isset($input['photo']) && ! empty($input['photo'])) {
                $user->clearMediaCollection(User::IMAGE_PATH);
                $user->addMedia($input['photo'])->toMediaCollection(User::IMAGE_PATH, config('app.media_disc'));
            }
            $user->update($input);
        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }

        return true;
    }

    /**
     * @param  array  $input
     * @return User|null
     *
     * @throws Exception
     */
    public function store($input)
    {
        
        if (! isset($input['password'])) {
            $input['password'] = Str::random(6);
            $input['password_confirmation'] = $input['password'];
        }
        $input = $this->validateInput($input);
        $input['created_by'] = getLoggedInUserId();
        $input['activation_code'] = uniqid();

        /** @var User $user */
        $user = User::create($input);

        if (! empty($input['photo'])) {
            $user->addMedia($input['photo'])->toMediaCollection(User::IMAGE_PATH, config('app.media_disc'));
        }

        $this->assignRolesAndProjects($user, $input);

        $user->sendEmailVerificationNotification();

        if (isset($input['password'])) {
            try {
                Mail::to($input['email'])
                    ->send(new SendUserPassword('emails.send_user_password',
                        'Your Login Credential',
                        $input));
            } catch (Exception $e) {
                throw new UnprocessableEntityHttpException($e->getMessage());
            }
        }

        if (! empty($input['project_ids'])) {
            foreach ($input['project_ids'] as $projectId) {
                $projects = Project::find($projectId);
                activity()
                    ->causedBy(getLoggedInUser())
                    ->withProperties(['modal' => User::class, 'data' => $projects->name])
                    ->performedOn($projects)
                    ->useLog('User Assigned to Project')
                    ->log('Assigned '.$input['name'].' to project');

                UserNotification::create([
                    'title' => 'New Project Assigned',
                    'description' => $projects->name.' assigned to you',
                    'type' => Project::class,
                    'user_id' => $user->id,
                ]);
            }
        }

        return $user->fresh();
    }

    /**
     * @param  array  $input
     * @param  int  $id
     * @return User
     *
     * @throws Exception
     */
    public function update($input, $id)
    {
        unset($input['password']);
        $input = $this->validateInput($input);

        /** @var User $user */
        $user = User::findOrFail($id);
        $user->update($input);

        if (! empty($input['photo'])) {
            $user->clearMediaCollection(User::IMAGE_PATH);
            $user->addMedia($input['photo'])->toMediaCollection(User::IMAGE_PATH, config('app.media_disc'));
        }

        $this->assignRolesAndProjects($user, $input);

        return $user->fresh();
    }

    /**
     * @param  array  $input
     * @return mixed
     */
    public function validateInput($input)
    {
        if (! empty($input['password']) && Auth::user()->can('manage_users')) {
            $input['password'] = Hash::make($input['password']);
        } else {
            unset($input['password']);
        }

        $input['is_active'] = (! empty($input['is_active'])) ? 1 : 0;

        return $input;
    }

    /**
     * @param  User  $user
     * @param  array  $input
     * @return bool
     */
    public function assignRolesAndProjects($user, $input)
    {
        $projectIds = ! empty($input['project_ids']) ? $input['project_ids'] : [];
        $user->projects()->sync($projectIds);

        $roles = ! empty($input['role_id']) ? $input['role_id'] : [];
        $user->roles()->sync($roles);

        return true;
    }

    /**
     * @param $id
     * @return bool
     *
     * @throws Exception
     */
    public function deleteTimeEntry($id)
    {
        $timeEntries = TimeEntry::whereUserId($id)->get();
        foreach ($timeEntries as $timeEntry) {
            $timeEntry->deleted_by = getLoggedInUserId();
            $timeEntry->save();
            $timeEntry->delete();
        }

        return true;
    }
}
