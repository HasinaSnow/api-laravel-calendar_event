<?php

namespace App\Http\Controllers;

use App\Helpers\AboutCurrentUser;
use App\Http\Requests\ClientRequest;
use App\Models\Client;
use App\Services\Response\ResponseService;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(
        ResponseService $responseService,
        AboutCurrentUser $aboutCurrentUser
    ) 
    {

        if($aboutCurrentUser->isAdmin())
            $clients = Client::orderby('id', 'desc')
                ->get(['id', 'name', 'created_by', 'updated_by', 'created_at', 'updated_at'])
                ->toArray();
        else if($aboutCurrentUser->isEventManager())
            $clients = Client::orderby('id', 'desc')
                ->where('created_by', $aboutCurrentUser->id())
                ->get(['id', 'name', 'created_by', 'updated_by', 'created_at', 'updated_at'])
                ->toArray();
        else
            return $responseService->notAuthorized();

        // send the response
        return $responseService->successfullGetted($clients, 'Client');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(
        ResponseService $responseService,
        ClientRequest $clientRequest,
        AboutCurrentUser $aboutCurrentUser,
        Client $client
    )
    {

        // verify the permission
        if(!$aboutCurrentUser->isPermisToCreate($client))
            return $responseService->notAuthorized();

        // store in the database
        $client = new Client;
        $client->name = $clientRequest->name;
        $client->infos = $clientRequest->infos;
        $client->created_by = $aboutCurrentUser->id();

        if (
            Client::where('name', $clientRequest->name)
                ->exists()
        ) 
            return $responseService->alreadyExist('Client');

        if($client->save())
            return $responseService->successfullStored('Client');

    }

    /**
     * Display the specified resource.
     */
    public function show(
        ResponseService $responseService, 
        AboutCurrentUser $aboutCurrentUser,
        Client $client
    )
    {
        // get data in db
        $data = ['client' => $client->toArray()];

        if($aboutCurrentUser->isAdmin())
        {
            $data['events'] = $client->events()->with(
                [
                    'services:id,name',
                    'category:id,name',
                    'place:id,name',
                    'type:id,name',
                    'confirmation:id,name'
                ]
            );
            
           
        } else if($aboutCurrentUser->isEventManager())
        {
            if($client->created_by === $aboutCurrentUser->id())
                $data['events'] = $client->events()
                ->with(
                    [
                        'services:id,name',
                        'category:id,name',
                        'place:id,name',
                        'type:id,name',
                        'confirmation:id,name'
                    ]
                )
                ->get()
                ->where('created_by', $aboutCurrentUser->id());
            else
                $data = [];
        } else
            return $responseService->notAuthorized();

        return $responseService->successfullGetted($data, 'Client');
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        ResponseService $responseService,
        ClientRequest $clientRequest,
        AboutCurrentUser $aboutCurrentUser,
        Client $client
    ) {
        // verify the permission
        if(!$aboutCurrentUser->isPermisToInteract($client))
            return $responseService->notAuthorized();

        // update in the database
        $client->name = $clientRequest->name;
        $client->infos = $clientRequest->infos;
        $client->updated_by = $aboutCurrentUser->id();

        if (
            Client::where('name', $clientRequest->name)
                ->exists()
        ) 
            return $responseService->alreadyExist('Client');

        if($client->update());
            return $responseService->successfullUpdated('Client');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        ResponseService $responseService,
        AboutCurrentUser $aboutCurrentUser,
        Client $client
    )
    {
        // verify the permission
        if(!$aboutCurrentUser->isPermisToInteract($client))
            return $responseService->notAuthorized();

            // try to delete the client
        if($client->delete())
            return $responseService->successfullDeleted('Client');
    }
}
