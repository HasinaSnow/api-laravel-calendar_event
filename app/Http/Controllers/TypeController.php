<?php

namespace App\Http\Controllers;

use App\Helpers\AboutUser;
use App\Http\Requests\TypeRequest;
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
    public function index(
        ResponseService $responseService
    )
    {
        $types = Type::orderby('id', 'desc')->get()->toArray();
        return $responseService->successfullGetted($types, 'List of Type');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(
        ResponseService $responseService,
        TypeRequest $typeRequest,
        AboutUser $aboutUser,
        Type $type
    )
    {
        // verify the permission
        if(!$aboutUser->isPermisToCreate($type))
            return $responseService->notAuthorized();

        // store in the database
        $type = new Type();
        $type->name = $typeRequest->name;
        $type->infos = $typeRequest->infos;
        $type->created_by = $aboutUser->id();

        if (
            Type::where('name', $typeRequest->name)
                ->exists()
        ) 
            return $responseService->alreadyExist('Type');

        if($type->save());
            return $responseService->successfullStored('Type');
    }

    /**
     * Display the specified resource.
     */
    public function show(
        ResponseService $responseService,
        AboutUser $aboutUser, 
        Type $type
    )
    {
        $data = ['type' => $type->toArray()];

        if($aboutUser->isAdmin())
        {
            $data['events'] = $type->events()->with(
                [
                    'services:id,name',
                    'category:id,name',
                    'place:id,name',
                    'type:id,name',
                    'confirmation:id,name'
                ]
            );
        }else if($aboutUser->isEventManager())
        {
            $data['events'] = $type->events()
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
            ->where('created_by', $aboutUser->id());
        }

        return $responseService->successfullGetted($data, 'Type');

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        ResponseService $responseService,
        TypeRequest $typeRequest,
        AboutUser $aboutUser,
        Type $type
    )
    {
        // verify the permission
        if(!$aboutUser->isPermisToInteract($type))
            return $responseService->notAuthorized();

        // store in the database
        $type->name = $typeRequest->name;
        $type->infos = $typeRequest->infos;
        $type->updated_by = $aboutUser->id();

        if (
            Type::where('name', $typeRequest->name)
                ->exists()
        ) 
            return $responseService->alreadyExist('Type');
        
        if($type->save());
            return $responseService->successfullUpdated('Type');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        ResponseService $responseService,
        AboutUser $aboutUser,
        Type $type
    )
    {
        // verify the permission
        if(!$aboutUser->isPermisToInteract($type))
            return $responseService->notAuthorized();

        if($type->delete())
         return $responseService->successfullDeleted('Type');
    }
}
