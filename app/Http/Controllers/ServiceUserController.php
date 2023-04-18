<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceUserRequest;
use App\Models\ServiceUser;
use App\Services\JWT\JWTService;
use App\Services\Permission\Voter\VoteService;
use App\Services\Response\ResponseService;
use Illuminate\Support\Facades\DB;

class ServiceUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(
        JWTService $jWTService,
        ResponseService $responseService
    ) {

        // recuperer tous les users appartenants au service dont l'user courant fait partie
        $userId = $jWTService->getIdUserToken();
        $serviceIdUsers = DB::select('select service_id from service_users where user_id = ?', [$userId]);
        foreach ($serviceIdUsers as $once)
        $serviceId[] = $once->service_id;

        $serviceUsers = ServiceUser::whereIn('service_id', $serviceId)->get()->toArray();

        // send the response
        return $responseService->generateResponseJson(
            'success',
            200,
            'All serviceUser successfully getted',
            $serviceUsers
        );

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(
        ResponseService $responseService,
        JWTService $jWTService,
        VoteService $permission,
        ServiceUser $serviceUser,
        ServiceUserRequest $serviceUserRequest

    ) {

        $attribute = ['create'];

        if (!$permission->resultVote($attribute, $serviceUser, $jWTService)) {
            // send the response error
            return $responseService->generateResponseJson(
                'error',
                404,
                'Not Authorized'
            );
        };
        
        // store in the database
        $serviceUser = new ServiceUser;
        $serviceUser->service_id = $serviceUserRequest->service_id;
        $serviceUser->user_id = $serviceUserRequest->user_id;
        $serviceUser->created_by = $jWTService->getIdUserToken();

        // verifie the unique record in database
        if (
            ServiceUser::where('service_id', $serviceUser->service_id)
            ->where('user_id', $serviceUser->user_id)
            ->exists()
        )
            return $responseService->generateResponseJson(
                'error',
                404,
                'the record is already exists in database',
            );


        if ($serviceUser->save());
            // send the response
            return $responseService->generateResponseJson(
                'success',
                200,
                'ServiceUser successfully saved',
            );
    }

    /**
     * Display the specified resource.
     */
    public function show(
        ResponseService $responseService,
        JWTService $jWTService,
        VoteService $permission,
        ServiceUser $serviceUser,
    ) {
        // send the response
        return $responseService->generateResponseJson(
            'success',
            200,
            'client successfully showed',
            $serviceUser->toArray()
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        ResponseService $responseService,
        JWTService $jWTService,
        VoteService $permission,
        ServiceUser $serviceUser,
        ServiceUserRequest $serviceUserRequest
    ) {
        $attribute = ['interact'];

        if (!$permission->resultVote($attribute, $serviceUser, $jWTService)) {
            // send the response error
            return $responseService->generateResponseJson(
                'error',
                404,
                'Not Authorized'
            );
        };

        // validate the data
        $serviceUser->service_id = $serviceUserRequest->service_id;
        $serviceUser->user_id = $serviceUserRequest->user_id;
        
        // verifie the unique record in database
        if (
            ServiceUser::where('service_id', $serviceUser->service_id)
            ->where('user_id', $serviceUser->user_id)
            ->exists()
            )
            return $responseService->generateResponseJson(
                'error',
                404,
                'the record is already exists in database',
            );
            
        $serviceUser->updated_by = $jWTService->getIdUserToken();
        if ($serviceUser->save());
        // send the response
        return $responseService->generateResponseJson(
            'success',
            200,
            'SErviceUser successfully saved',
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        ResponseService $responseService,
        JWTService $jWTService,
        VoteService $permission,
        ServiceUser $serviceUser
    ) {
        $attribute = ['interact'];

        if (!$permission->resultVote($attribute, $serviceUser, $jWTService)) {
            // send the response error
            return $responseService->generateResponseJson(
                'error',
                404,
                'Not Authorized'
            );
        };

        if (!isset($serviceUser))
            return $responseService->generateResponseJson(
                'error',
                404,
                'ServiceUser not found'
            );

            $serviceUser->delete();
            return $responseService->generateResponseJson(
                'succes',
                200,
                'ServiceUser successfully deleted'
            );
    }
}
