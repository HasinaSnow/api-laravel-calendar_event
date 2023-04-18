<?php

namespace App\Http\Controllers;

use App\Models\Confirmation;
use App\Services\JWT\JWTService;
use App\Services\Permission\Voter\VoteService;
use App\Services\Response\ResponseService;
use Illuminate\Http\Request;

class ConfirmationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ResponseService $responseService)
    {
        $confirmations = Confirmation::orderby('id', 'desc')->get()->toArray();

        // send the response
        return $responseService->generateResponseJson(
            'success',
            200,
            'All confirmation successfully getted',
            $confirmations
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
        Confirmation $confirmation
    )
    {
        $attribute = ['create'];

        // verifie the permission
        if (!$permission->resultVote($attribute, $confirmation, $jWTService)) {
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
        $confirmation = new Confirmation();
        $confirmation->name = $request->name;
        $confirmation->infos = $request->infos;
        $confirmation->created_by = $jWTService->getIdUserToken();

        if($confirmation->save());
            // send the response
            return $responseService->generateResponseJson(
                'success',
                200,
                'confirmation successfully saved',
            );
    }

    /**
     * Display the specified resource.
     */
    public function show(
        ResponseService $responseService,
        Confirmation $confirmation
    )
    {
        // send the response
        return $responseService->generateResponseJson(
            'success',
            200,
            'Confirmation successfully showed',
            $confirmation->toArray()
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
        Confirmation $confirmation)
    {
        $attribute = ['interact'];

        if (!$permission->resultVote($attribute, $confirmation, $jWTService)) {
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
        $confirmation->name = $request->name;
        $confirmation->infos = $request->infos;
        $confirmation->updated_by = $jWTService->getIdUserToken();
        
        if($confirmation->save());
            // send the response
            return $responseService->generateResponseJson(
                'success',
                200,
                'Confirmation successfully saved',
            );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        ResponseService $responseService,
        JWTService $jWTService,
        VoteService $permission,
        Confirmation $confirmation)
    {
        $attribute = ['interact'];

        if (!$permission->resultVote($attribute, $confirmation, $jWTService)) {
            // send the response error
            return $responseService->generateResponseJson(
                'error',
                404,
                'Not Authorized'
            );
        };

        if (!isset($confirmation))
            return $responseService->generateResponseJson(
                'error',
                404,
                'Confirmation not found'
            );

        $confirmation->delete();
        return $responseService->generateResponseJson(
            'succes',
            200,
            'Confirmation successfully deleted'
        );
    }
}
