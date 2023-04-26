<?php

namespace App\Http\Controllers;

use App\Helpers\AboutCurrentUser;
use App\Http\Requests\EquipementRequest;
use App\Models\Equipement;
use App\Services\Response\ResponseService;
use Illuminate\Http\Request;

class EquipementController extends Controller
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
            $equipements = Equipement::orderby('id', 'desc')->get()->toArray();
        else if($aboutCurrentUser->isEquipementManager())
            $equipements = Equipement::orderby('id', 'desc')
                ->whereIn('service_id', $aboutCurrentUser->idServices())
                ->with('service:id,name')
                ->get()->toArray();
        else
            return $responseService->notAuthorized();

        return $responseService->successfullGetted($equipements, 'Equipements');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(
        ResponseService $responseService,
        EquipementRequest $equipementRequest,
        AboutCurrentUser $aboutCurrentUser,
        Equipement $equipement
    )
    {
        // verify the permission
        if(!$aboutCurrentUser->isPermisToCreate($equipement))
            return $responseService->notAuthorized();

        // store in the database
        $equipement = new Equipement();
        $equipement->name = $equipementRequest->name;
        $equipement->price = $equipementRequest->price;
        $equipement->infos = $equipementRequest->infos;
        $equipement->service_id = $equipementRequest->service_id;
        $equipement->created_by = $aboutCurrentUser->id();

        // verify if the service_id
        if(
            !$aboutCurrentUser->isAdmin() &&
            !in_array($equipementRequest->service_id, $aboutCurrentUser->idServices())
        )
            return $responseService->errorServer();
        
        // verify if data already exist in db
        if (
            Equipement::where('name', $equipementRequest->name)
                ->where('service_id', $equipementRequest->service_id)
                ->exists()
        ) 
            return $responseService->alreadyExist('Equipement');
 
        if ($equipement->save());
            return $responseService->successfullStored('Equipement');

    }

    /**
     * Display the specified resource.
     */
    public function show(
        ResponseService $responseService,
        AboutCurrentUser $aboutCurrentUser,
        Equipement $equipement
    )
    {
        $datas = [
            'equipement' => $equipement->toArray(),
            'service' => $equipement->service()->get()->toArray()
        ];

        // verify the permission
        if($aboutCurrentUser->isAdmin())
            return $responseService->successfullGetted($datas, 'Equipement');
        else if($aboutCurrentUser->isEquipementManager())
            if(in_array($equipement->service_id, $aboutCurrentUser->idServices()))
                return $responseService->successfullGetted($datas, 'Equipement');

        return $responseService->notFound();

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        ResponseService $responseService,
        EquipementRequest $equipementRequest,
        AboutCurrentUser $aboutCurrentUser,
        Equipement $equipement
    )
    {
        if(!$aboutCurrentUser->isPermisToInteract($equipement))
            return $responseService->notAuthorized();

        // updated in the database
        $equipement->name = $equipementRequest->name;
        $equipement->infos = $equipementRequest->infos;
        $equipement->service_id = $equipementRequest->service_id;
        $equipement->updated_by = $aboutCurrentUser->id();

        // verify service
        if(
            !$aboutCurrentUser->isAdmin() &&
            !in_array($equipementRequest->service_id, $aboutCurrentUser->idServices())
        )
            return $responseService->errorServer();

        if (
            Equipement::where('name', $equipementRequest->name)
                ->where('service_id', $equipementRequest->service_id)
                ->exists()
        ) 
            return $responseService->alreadyExist('Equipement');
 
        if ($equipement->save())
            return $responseService->successfullUpdated('Equipement');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        ResponseService $responseService,
        AboutCurrentUser $aboutCurrentUser,
        Equipement $equipement
    )
    {
        if(!$aboutCurrentUser->isPermisToInteract($equipement))
            return $responseService->notAuthorized();

        if($equipement->delete())
            return $responseService->successfullDeleted('Equipement');
        
    }
}
