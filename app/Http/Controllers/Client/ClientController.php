<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UpdateUserProfileRequest;
use App\Models\Client;
use App\Models\User;
use App\Repositories\ClientRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ClientController extends AppBaseController
{
    private $clientRepository;

    /**
     * ClientController constructor.
     *
     * @param  ClientRepository  $clientRepo
     */
    public function __construct(ClientRepository $clientRepo)
    {
        $this->clientRepository = $clientRepo;
    }

    /**
     * @param  int  $id
     * @return JsonResponse
     */
    public function edit($id)
    {
        $client = Client::whereUserId($id)->first();

        return $this->sendResponse($client, 'Client retrieved successfully.');
    }

    /**
     * @param  UpdateUserProfileRequest  $request
     * @return JsonResponse
     */
    public function profileUpdate(UpdateUserProfileRequest $request)
    {
        $input = $request->all();
        $this->clientRepository->profileUpdate($input);

        return $this->sendSuccess('Profile updated successfully.');
    }

    /**
     * @param  ChangePasswordRequest  $request
     * @return JsonResponse
     */
    public function changePassword(ChangePasswordRequest $request)
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
}
