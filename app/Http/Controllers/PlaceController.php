<?php

namespace App\Http\Controllers;

use App\Models\Place;
use App\Services\JWT\JWTService;
use App\Services\Permission\Voter\VoteService;
use App\Services\Response\ResponseService;
use Illuminate\Http\Request;

class PlaceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ResponseService $responseService)
    {
        $places = Place::orderby('id', 'desc')->get()->toArray();

        // send the response
        return $responseService->generateResponseJson(
            'success',
            200,
            'All places successfully getted',
            $places
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
        Place $place
    )
    {
        $attribute = ['create'];

        // verifie the permission
        if (!$permission->resultVote($attribute, $place, $jWTService)) {
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
        $place = new Place();
        $place->name = $request->name;
        $place->infos = $request->infos;
        $place->created_by = $jWTService->getIdUserToken();

        if($place->save());
            // send the response
            return $responseService->generateResponseJson(
                'success',
                200,
                'place successfully saved',
            );

    }

    /**
     * Display the specified resource.
     */
    public function show(
        ResponseService $responseService, 
        Place $place
    )
    {
        // send the response
        return $responseService->generateResponseJson(
            'success',
            200,
            'place successfully showed',
            $place->toArray()
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
        Place $place
    )
    {
        $attribute = ['interact'];

        if (!$permission->resultVote($attribute, $place, $jWTService)) {
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
        $place->name = $request->name;
        $place->infos = $request->infos;
        $place->updated_by = $jWTService->getIdUserToken();
        
        if($place->save());
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
        Place $place
    )
    {
        $attribute = ['interact'];

        if (!$permission->resultVote($attribute, $place, $jWTService)) {
            // send the response error
            return $responseService->generateResponseJson(
                'error',
                404,
                'Not Authorized'
            );
        };

        if (!isset($place))
            return $responseService->generateResponseJson(
                'error',
                404,
                'Place not found'
            );

        $place->delete();
        return $responseService->generateResponseJson(
            'succes',
            200,
            'Place successfully deleted'
        );
    }
}
