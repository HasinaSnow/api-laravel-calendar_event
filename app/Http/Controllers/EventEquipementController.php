<?php

namespace App\Http\Controllers;

use App\Helpers\AboutCurrentUser;
use App\Http\Requests\AttachEquipementListRequest;
use App\Http\Requests\DetachEquipementListRequest;
use App\Http\Requests\UpdateEquipementEventRequest;
use App\Models\Equipement;
use App\Models\Event;
use App\Services\Response\ResponseService;

class EventEquipementController extends Controller
{
    /**
     * Display the tasks attributed to the specified event.
     */
    public function showEquipementListAttached(
        ResponseService $responseService,
        AboutCurrentUser $aboutCurrentUser,
        Event $event
    )
    {
        // verify the permission
        if(!($aboutCurrentUser->isAdmin() || $aboutCurrentUser->isEquipementManager()))
            return $responseService->notAuthorized();

        $datas = $event->equipements()->get()->toArray();

        return $responseService->successfullGetted($datas, 'Tasks of event');
            
    }

    /**
     * Attach the list of equipement in the specified event
     */
    public function attachEquipementList(
        ResponseService $responseService,
        AttachEquipementListRequest $attachEquipementListRequest,
        AboutCurrentUser $aboutCurrentUser,
        Event $event,
    )
    {
        // verifie the permission
        if(!($aboutCurrentUser->isAdmin() || $aboutCurrentUser->isEquipementManager()))
            return $responseService->notAuthorized();

        // list of equipements 
        foreach($attachEquipementListRequest->equipements as $equipement)
        {
            $serviceToAttach[$equipement['id']] = [
                'created_by' => $aboutCurrentUser->id(),
            ];

            if(isset($equipement['quantity']))
            {
                $serviceToAttach[$equipement['id']]['quantity'] = $equipement['quantity'];
                $serviceToAttach[$equipement['id']]['amount'] = ($equipement['quantity'] * Equipement::find($equipement['id'])->price);
            }
        }

        // attach all the list of tasks
        if($event->equipements()->syncWithoutDetaching($serviceToAttach))
            return $responseService->successfullAttached(
                count($attachEquipementListRequest->equipements) . ' Equipements(s)', 
                $event->date
            );

        // send error serv
        return $responseService->errorServer();
    }

    public function updateEquipementEvent(
        ResponseService $responseService,
        AboutCurrentUser $aboutCurrentUser,
        UpdateEquipementEventRequest $updateEquipementEventRequest,
        Event $event,
        Equipement $equipement
    )
    {
        if(!$aboutCurrentUser->isAdmin())
            return  $responseService->notAuthorized();

        $eventEquipement = $event->equipements()->updateExistingPivot( $equipement->id, [
            'amount' => $updateEquipementEventRequest->amount,
            ''
        ]);

        // dd($eventEquipement->get()[0]->toArray()["pivot"]['amount']);
        

        
    }

    /**
     * Detach the list of tasks in the specified event
     */
    public function detachEquipementList(
        ResponseService $responseService,
        DetachEquipementListRequest $detachEquipementListRequest,
        AboutCurrentUser $aboutCurrentUser,
        Event $event,
    )
    {
        if(!($aboutCurrentUser->isAdmin() || $aboutCurrentUser->isEquipementManager()))
            return $responseService->notAuthorized();

        if($event->equipements()->detach($detachEquipementListRequest->equipements))
            return $responseService->successfullDetached(
                count($detachEquipementListRequest->equipements) . ' Equipements(s)', 
                $event->date
            );
        
        return $responseService->errorServer();
    }
    

}
