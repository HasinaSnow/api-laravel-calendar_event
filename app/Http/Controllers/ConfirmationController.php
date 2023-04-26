<?php

namespace App\Http\Controllers;

use App\Helpers\AboutCurrentUser;
use App\Http\Requests\ConfirmationRequest;
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
    public function index(
        ResponseService $responseService
    )
    {
        $confirmations = Confirmation::orderby('id', 'desc')->get()->toArray();
        return $responseService->successfullGetted($confirmations, 'List of Confirmation');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(
        ResponseService $responseService,
        ConfirmationRequest $confirmationRequest,
        AboutCurrentUser $aboutCurrentUser,
        Confirmation $confirmation
    )
    {
        // verify the permission
        if(!$aboutCurrentUser->isPermisToCreate($confirmation))
            return $responseService->notAuthorized();

        // store in the database
        $confirmation = new Confirmation();
        $confirmation->name = $confirmationRequest->name;
        $confirmation->infos = $confirmationRequest->infos;
        $confirmation->created_by = $aboutCurrentUser->id();

        if (
            Confirmation::where('date', $confirmationRequest->date)
                ->where('name', $confirmationRequest->category_id)
                ->where('infos', $confirmationRequest->place_id)
                ->exists()
        ) 
            return $responseService->alreadyExist('Confirmation');

        if($confirmation->save());
            return $responseService->successfullStored('Confirmation');
    }

    /**
     * Display the specified resource.
     */
    public function show(
        ResponseService $responseService,
        AboutCurrentUser $aboutCurrentUser, 
        Confirmation $confirmation
    )
    {
        $data = ['confirmation' => $confirmation->toArray()];

        if($aboutCurrentUser->isAdmin())
        {
            $data['events'] = $confirmation->events()->with(
                [
                    'services:id,name',
                    'category:id,name',
                    'place:id,name',
                    'type:id,name',
                    'confirmation:id,name'
                ]
            );
        }else if($aboutCurrentUser->isEventManager())
        {
            $data['events'] = $confirmation->events()
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
        }

        return $responseService->successfullGetted($data, 'Confirmation');

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        ResponseService $responseService,
        ConfirmationRequest $confirmationRequest,
        AboutCurrentUser $aboutCurrentUser,
        Confirmation $confirmation
    )
    {
        // verify the permission
        if(!$aboutCurrentUser->isPermisToInteract($confirmation))
            return $responseService->notAuthorized();

        // store in the database
        $confirmation->name = $confirmationRequest->name;
        $confirmation->infos = $confirmationRequest->infos;
        $confirmation->updated_by = $aboutCurrentUser->id();

        if (
            Confirmation::where('date', $confirmationRequest->date)
                ->where('name', $confirmationRequest->category_id)
                ->where('infos', $confirmationRequest->place_id)
                ->exists()
        ) 
            return $responseService->alreadyExist('Confirmation');
        
        if($confirmation->save());
            return $responseService->successfullUpdated('Confirmation');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        ResponseService $responseService,
        AboutCurrentUser $aboutCurrentUser,
        Confirmation $confirmation
    )
    {
        // verify the permission
        if(!$aboutCurrentUser->isPermisToInteract($confirmation))
            return $responseService->notAuthorized();

        if($confirmation->delete())
         return $responseService->successfullDeleted('Confirmation');
    }
}
