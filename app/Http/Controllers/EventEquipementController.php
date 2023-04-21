<?php

namespace App\Http\Controllers;

use App\Helpers\AboutUser;
use App\Http\Requests\AttachEquipementListRequest;
use App\Http\Requests\DetachEquipementListRequest;
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
        AboutUser $aboutUser,
        Event $event
    )
    {
        // verify the permission
        if(!($aboutUser->isAdmin() || $aboutUser->isEquipementManager()))
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
        AboutUser $aboutUser,
        Event $event,
    )
    {
        // verifie the permission
        if(!($aboutUser->isAdmin() || $aboutUser->isEquipementManager()))
            return $responseService->notAuthorized();

        // dd(Equipement::find(4)->price);

        // list of equipemnts 
        foreach($attachEquipementListRequest->equipements as $equipement)
        {
            $serviceToAttach[$equipement['id']] = [
                'created_by' => $aboutUser->id(),
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

    /**
     * Detach the list of tasks in the specified event
     */
    public function detachEquipementList(
        ResponseService $responseService,
        DetachEquipementListRequest $detachEquipementListRequest,
        AboutUser $aboutUser,
        Event $event,
    )
    {
        if(!($aboutUser->isAdmin() || $aboutUser->isEquipementManager()))
            return $responseService->notAuthorized();

        if($event->equipements()->detach($detachEquipementListRequest->equipements))
            return $responseService->successfullDetached(
                count($detachEquipementListRequest->equipements) . ' Equipements(s)', 
                $event->date
            );
        
        return $responseService->errorServer();
    }
    

}
