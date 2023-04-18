<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventServiceRequest;
use App\Models\EventService;
use App\Services\JWT\JWTService;
use App\Services\Permission\Voter\VoteService;
use App\Services\Response\ResponseService;
use Illuminate\Support\Facades\DB;

class EventServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(
        JWTService $jWTService,
        ResponseService $responseService
    ) {
        // recuperer tous les events appartenant un service dont l'user fait partie
        $userId = $jWTService->getIdUserToken();
        $serviceIdUsers = DB::select('select service_id from service_users where user_id = ?', [$userId]);
        foreach ($serviceIdUsers as $once)
            $serviceId[] = $once->service_id;
        // $serviceId =implode(',', $serviceId);

        $eventService = EventService::whereIn('service_id', $serviceId)->get()->toArray();
        // send the response
        return $responseService->generateResponseJson(
            'success',
            200,
            'All serviceUser successfully getted',
            $eventService
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(
        ResponseService $responseService,
        JWTService $jWTService,
        VoteService $permission,
        EventService $eventService,
        EventServiceRequest $eventServiceRequest
    ) {
        $attribute = ['create'];

        if (!$permission->resultVote($attribute, $eventService, $jWTService)) {
            // send the response error
            return $responseService->generateResponseJson(
                'error',
                404,
                'Not Authorized'
            );
        };

        // store in the database
        $eventService = new EventService;
        $eventService->service_id = $eventServiceRequest->service_id;
        $eventService->event_id = $eventServiceRequest->event_id;
        $eventService->created_by = $jWTService->getIdUserToken();

        // verifie the unique record in database
        if (
            EventService::where('service_id', $eventService->service_id)
            ->where('event_id', $eventService->event_id)
            ->exists()
        )
            return $responseService->generateResponseJson(
                'error',
                404,
                'the record is already exists in database',
            );

        if ($eventService->save());
        // send the response
        return $responseService->generateResponseJson(
            'success',
            200,
            'EventService successfully saved',
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(
        ResponseService $responseService,
        JWTService $jWTService,
        VoteService $permission,
        EventService $eventService
    ) {
        $attribute = ['intercat'];

        if (!$permission->resultVote($attribute, $eventService, $jWTService)) {
            // send the response error
            return $responseService->generateResponseJson(
                'error',
                404,
                'Not Authorized'
            );
        };

        // send the response
        return $responseService->generateResponseJson(
            'success',
            200,
            'client successfully showed',
            $eventService->toArray()
        );
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(
        ResponseService $responseService,
        JWTService $jWTService,
        VoteService $permission,
        EventService $eventService,
        EventServiceRequest $eventServiceRequest
    ) {
        $attribute = ['interact'];

        if (!$permission->resultVote($attribute, $eventService, $jWTService)) {
            // send the response error
            return $responseService->generateResponseJson(
                'error',
                404,
                'Not Authorized'
            );
        };

        // record the data
        $eventService->service_id = $eventServiceRequest->service_id;
        $eventService->event_id = $eventServiceRequest->event_id;
        $eventService->updated_by = $jWTService->getIdUserToken();

        // verifie the unique record in database
        if (
            EventService::where('service_id', $eventService->service_id)
            ->where('event_id', $eventService->event_id)
            ->exists()
        )
            return $responseService->generateResponseJson(
                'error',
                404,
                'the record is already exists in database',
            );

        if ($eventService->save());
        // send the response
        return $responseService->generateResponseJson(
            'success',
            200,
            'EventService successfully saved',
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        ResponseService $responseService,
        JWTService $jWTService,
        VoteService $permission,
        EventService $eventService,
    ) {
        $attribute = ['interact'];

        if (!$permission->resultVote($attribute, $eventService, $jWTService)) {
            // send the response error
            return $responseService->generateResponseJson(
                'error',
                404,
                'Not Authorized'
            );
        };

        if (!isset($eventService))
            return $responseService->generateResponseJson(
                'error',
                404,
                'EventService not found'
            );

        $eventService->delete();
        return $responseService->generateResponseJson(
            'succes',
            200,
            'EventService successfully deleted'
        );
    }
}
