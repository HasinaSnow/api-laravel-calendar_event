<?php

namespace App\Http\Controllers;

use App\Models\Type;
use App\Services\JWT\JWTService;
use App\Services\Permission\Voter\VoteService;
use App\Services\Response\ResponseService;
use Illuminate\Http\Request;

class TypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ResponseService $responseService)
    {
        $types = Type::orderby('id', 'desc')->get()->toArray();

        // send the response
        return $responseService->generateResponseJson(
            'success',
            200,
            'All types successfully getted',
            $types
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
        Type $type
    )
    {
        $attribute = ['create'];

        // verifie the permission
        if (!$permission->resultVote($attribute, $type, $jWTService)) {
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
        $type = new Type();
        $type->name = $request->name;
        $type->infos = $request->infos;
        $type->created_by = $jWTService->getIdUserToken();

        if($type->save());
            // send the response
            return $responseService->generateResponseJson(
                'success',
                200,
                'Type successfully saved',
            );
    }

    /**
     * Display the specified resource.
     */
    public function show(
        ResponseService $responseService, 
        Type $type
    )
    {
        // send the response
        return $responseService->generateResponseJson(
            'success',
            200,
            'Type successfully showed',
            $type->toArray()
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
        Type $type
    )
    {
        $attribute = ['interact'];

        if (!$permission->resultVote($attribute, $type, $jWTService)) {
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
        $type->name = $request->name;
        $type->infos = $request->infos;
        $type->updated_by = $jWTService->getIdUserToken();
        
        if($type->save());
            // send the response
            return $responseService->generateResponseJson(
                'success',
                200,
                'Type successfully saved',
            );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        ResponseService $responseService,
        JWTService $jWTService,
        VoteService $permission,
        Type $type
    )
    {
        $attribute = ['interact'];

        if (!$permission->resultVote($attribute, $type, $jWTService)) {
            // send the response error
            return $responseService->generateResponseJson(
                'error',
                404,
                'Not Authorized'
            );
        };

        if (!isset($type))
            return $responseService->generateResponseJson(
                'error',
                404,
                'Type not found'
            );

        $type->delete();
        return $responseService->generateResponseJson(
            'succes',
            200,
            'Type successfully deleted'
        );
    }
}
