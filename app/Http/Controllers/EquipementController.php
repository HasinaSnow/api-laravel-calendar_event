<?php

namespace App\Http\Controllers;

use App\Helpers\AboutUser;
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
        AboutUser $aboutUser
    )
    {

        if($aboutUser->isAdmin())
            $equipements = Equipement::orderby('id', 'desc')->get()->toArray();
        else if($aboutUser->isEquipementManager())
            $equipements = Equipement::orderby('id', 'desc')
                ->whereIn('service_id', $aboutUser->idServices())
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
        AboutUser $aboutUser,
        Equipement $equipement
    )
    {
        // verify the permission
        if(!$aboutUser->isPermisToCreate($equipement))
            return $responseService->notAuthorized();

        // store in the database
        $equipement = new Equipement();
        $equipement->name = $equipementRequest->name;
        $equipement->price = $equipementRequest->price;
        $equipement->infos = $equipementRequest->infos;
        $equipement->service_id = $equipementRequest->service_id;
        $equipement->created_by = $aboutUser->id();

        // verify if the service_id
        if(
            !$aboutUser->isAdmin() &&
            !in_array($equipementRequest->service_id, $aboutUser->idServices())
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
        AboutUser $aboutUser,
        Equipement $equipement
    )
    {
        $datas = [
            'equipement' => $equipement->toArray(),
            'service' => $equipement->service()->get()->toArray()
        ];

        // verify the permission
        if($aboutUser->isAdmin())
            return $responseService->successfullGetted($datas, 'Equipement');
        else if($aboutUser->isEquipementManager())
            if(in_array($equipement->service_id, $aboutUser->idServices()))
                return $responseService->successfullGetted($datas, 'Equipement');

        return $responseService->notFound();

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        ResponseService $responseService,
        EquipementRequest $equipementRequest,
        AboutUser $aboutUser,
        Equipement $equipement
    )
    {
        if(!$aboutUser->isPermisToInteract($equipement))
            return $responseService->notAuthorized();

        // updated in the database
        $equipement->name = $equipementRequest->name;
        $equipement->infos = $equipementRequest->infos;
        $equipement->service_id = $equipementRequest->service_id;
        $equipement->updated_by = $aboutUser->id();

        // verify service
        if(
            !$aboutUser->isAdmin() &&
            !in_array($equipementRequest->service_id, $aboutUser->idServices())
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
        AboutUser $aboutUser,
        Equipement $equipement
    )
    {
        if(!$aboutUser->isPermisToInteract($equipement))
            return $responseService->notAuthorized();

        if($equipement->delete())
            return $responseService->successfullDeleted('Equipement');
        
    }
}
