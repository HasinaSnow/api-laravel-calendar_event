<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Services\JWT\JWTService;
use App\Services\Permission\Voter\VoteService;
use App\Services\Response\ResponseService;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(
        ResponseService $responseService
    ) {

        // get all clients in the database
        $clients = Client::orderby('id', 'desc')->get()->toArray();

        // send the response
        return $responseService->generateResponseJson(
            'success',
            200,
            'All clients successfully getted',
            $clients
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(
        ResponseService $responseService,
        Request $request,
        JWTService $jWTService,
        VoteService $permission,
        Client $client
    )
    {

        $attribute = ['create'];

        if (!$permission->resultVote($attribute, $client, $jWTService)) {
            // send the response error
            return $responseService->generateResponseJson(
                'error',
                404,
                'Not Authorized'
            );
        };

        // verifie the datas sended
        if (!isset($request->name, $request->infos))
            return $responseService->generateResponseJson(
                'error',
                401,
                'Missing data'
            );

        // validate the data
        $this->validate($request, array(
            'name' => 'required|string|max:255',
            'infos' => 'string'
        ));

        // store in the database
        $client = new Client;
        $client->name = $request->name;
        $client->infos = $request->infos;
        $client->created_by = $jWTService->getIdUserToken();

        if($client->save());
            // send the response
            return $responseService->generateResponseJson(
                'success',
                200,
                'client successfully saved',
            );

    }

    /**
     * Display the specified resource.
     */
    public function show(ResponseService $responseService, Client $client)
    {

        // send the response
        return $responseService->generateResponseJson(
            'success',
            200,
            'client successfully showed',
            $client->toArray()
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        ResponseService $responseService,
        Request $request,
        JWTService $jWTService,
        VoteService $permission,
        Client $client
    ) {

        $attribute = ['interact'];

        if (!$permission->resultVote($attribute, $client, $jWTService)) {
            // send the response error
            return $responseService->generateResponseJson(
                'error',
                404,
                'Not Authorized'
            );
        };

        // verifie the datas sended
        if (!isset($request->name, $request->infos))
            return $responseService->generateResponseJson(
                'error',
                401,
                'Missing data'
            );

        // validate the data
        $this->validate($request, array(
            'name' => 'required|string|max:255',
            'infos' => 'string'
        ));

        // store in the database
        $client->name = $request->name;
        $client->infos = $request->infos;
        $client->updated_by = $jWTService->getIdUserToken();

        if($client->save());
            // send the response
            return $responseService->generateResponseJson(
                'success',
                200,
                'client successfully saved',
            );

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        ResponseService $responseService,
        JWTService $jWTService,
        VoteService $permission,
        Client $client
    )
    {
        $attribute = ['interact'];

        if (!$permission->resultVote($attribute, $client, $jWTService)) {
            // send the response error
            return $responseService->generateResponseJson(
                'error',
                404,
                'Not Authorized'
            );
        };

        if (!isset($client))
            return $responseService->generateResponseJson(
                'error',
                404,
                'Client not found'
            );

        $client->delete();
        return $responseService->generateResponseJson(
            'succes',
            200,
            'Client successfully deleted'
        );
    }
}
