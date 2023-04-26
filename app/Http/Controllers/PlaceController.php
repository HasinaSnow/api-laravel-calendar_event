<?php

namespace App\Http\Controllers;

use App\Helpers\AboutCurrentUser;
use App\Http\Requests\PlaceRequest;
use App\Models\Place;
use App\Services\Response\ResponseService;

class PlaceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(
        ResponseService $responseService,
        AboutCurrentUser $aboutCurrentUser
    ) 
    {

        $places = Place::orderby('id', 'desc')
            ->get(['id', 'name', 'created_by', 'updated_by', 'created_at', 'updated_at'])
            ->toArray();

        // send the response
        return $responseService->successfullGetted($places, 'Place');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(
        ResponseService $responseService,
        PlaceRequest $placeRequest,
        AboutCurrentUser $aboutCurrentUser,
        Place $place
    )
    {
        // verify the permission
        if(!$aboutCurrentUser->isPermisToCreate($place))
            return $responseService->notAuthorized();

        // store in the database
        $place = new Place;
        $place->name = $placeRequest->name;
        $place->infos = $placeRequest->infos;
        $place->created_by = $aboutCurrentUser->id();

        if (
            Place::where('name', $placeRequest->name)
                ->exists()
        ) 
            return $responseService->alreadyExist('Place');

        if($place->save())
            return $responseService->successfullStored('Place');

    }

    /**
     * Display the specified resource.
     */
    public function show(
        ResponseService $responseService, 
        AboutCurrentUser $aboutCurrentUser,
        Place $place
    )
    {
        // get data in db
        $data = ['place' => $place->toArray()];

        if($aboutCurrentUser->isAdmin())
        {
            $data['events'] = $place->events()->with(
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
            if($place->created_by === $aboutCurrentUser->id())
                $data['events'] = $place->events()
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

        return $responseService->successfullGetted($data, 'Place');
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        ResponseService $responseService,
        PlaceRequest $placeRequest,
        AboutCurrentUser $aboutCurrentUser,
        Place $place
    ) {
        // verify the permission
        if(!$aboutCurrentUser->isPermisToInteract($place))
            return $responseService->notAuthorized();

        // update in the database
        $place->name = $placeRequest->name;
        $place->infos = $placeRequest->infos;
        $place->updated_by = $aboutCurrentUser->id();

        if (
            Place::where('name', $placeRequest->name)
                ->exists()
        ) 
            return $responseService->alreadyExist('Place');

        if($place->update());
            return $responseService->successfullUpdated('Place');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        ResponseService $responseService,
        AboutCurrentUser $aboutCurrentUser,
        Place $place
    )
    {
        // verify the permission
        if(!$aboutCurrentUser->isPermisToInteract($place))
            return $responseService->notAuthorized();

            // try to delete the place
        if($place->delete())
            return $responseService->successfullDeleted('Place');
    }
}
