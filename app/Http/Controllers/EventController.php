<?php

namespace App\Http\Controllers;

use App\Helpers\AboutRole;
use App\Helpers\AboutUser;
use App\Http\Requests\EventRequest;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Client;
use App\Models\Confirmation;
use App\Models\Event;
use App\Models\Invoice;
use App\Models\Pack;
use App\Models\Place;
use App\Models\Service;
use App\Models\Type;
use App\Services\JWT\JWTService;
use App\Services\Permission\Voter\VoteService;
use App\Services\Response\ResponseService;
use Carbon\Exceptions\Exception;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(
        ResponseService $responseService,
        JWTService $jWTService,
        AboutUser $aboutUser,
        AboutRole $aboutRole,
    ) {
        
        // aboutuser
        $userId = $jWTService->getIdUserToken();
        $userRoles = $aboutUser->idUserRoles($userId); // 5
        $userServices = $aboutUser->idUserServices($userId); // [4,3]

        $events = Event::with(
            [
                'client:id,name',
                'type:id,name',
                'category:id,name',
                'place:id,name',
                'confirmation:id,name',
                'services:id,name' //with pivot
            ]
        );

        if (in_array($aboutRole->idRoleAdmin(), $userRoles)) {
            $events = $events->with('budget:id,event_id,amount', 'pack:id,name');
        } else {
            if (in_array($aboutRole->idRoleEventManager(), $userRoles)) {
                $events = $events
                    ->with('pack:id,name')
                    ->where('audience', true) 
                    ->orWhere('created_by', $userId);
            } else {
                $events = $events
                    ->where('audience', true)
                    ->whereRelation('services', 'service_id', '=', implode('||', $userServices));
            }
        }

        // send the response
        return $responseService->generateResponseJson(
            'success',
            200,
            'All events successfully getted',
            $events->get()->toArray()
        );
    }

    public function create(
        ResponseService $responseService,
        JWTService $jWTService,
        VoteService $permission,
        Event $event
    )
    {
        // verify the permission
        $attribute = ['create'];
        if (!$permission->resultVote($attribute, $event, $jWTService)) {
            // send the response error
            return $responseService->generateResponseJson(
                'error',
                404,
                'Not Authorized'
            );
        };

        $datas = [
            'services' => Service::all(['id', 'name', 'infos'])->toArray(),
            'clients' => Client::all(['id', 'name', 'infos'])->toArray(),
            'types' => Type::all(['id', 'name', 'infos'])->toArray(),
            'confirmations' => Confirmation::all(['id', 'name', 'infos'])->toArray(),
            'categories' => Category::all(['id', 'name', 'infos'])->toArray(),
            'places' => Place::all(['id', 'name', 'infos'])->toArray(),
            'packs' => Pack::all(['id', 'name', 'infos'])->toArray()
        ];

        return $responseService->generateResponseJson(
            'success',
            200,
            'Data succeffully getted',
            $datas
        );

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(
        ResponseService $responseService,
        EventRequest $eventRequest,
        JWTService $jWTService,
        VoteService $permission,
        Event $event
    ) {

        // verify the permission
        $attribute = ['create'];
        if (!$permission->resultVote($attribute, $event, $jWTService)) {
            // send the response error
            return $responseService->generateResponseJson(
                'error',
                404,
                'Not Authorized'
            );
        };

        // record the datas
        $event = new Event();
        $event->date = $eventRequest->date;
        $event->audience = $eventRequest->audience;

        $event->client_id = $eventRequest->client_id;
        $event->place_id = $eventRequest->place_id;
        $event->category_id = $eventRequest->category_id;
        $event->type_id = $eventRequest->type_id;
        $event->confirmation_id = $eventRequest->confirmation_id;
        $event->pack_id = $eventRequest->pack_id;

        $event->created_by = $jWTService->getIdUserToken();

        // verifie the unique record in database
        if (
            Event::where('date', $event->date)
            ->where('category_id', $event->category_id)
            ->where('place_id', $event->place_id)
            ->where('type_id', $event->type_id)
            ->where('confirmation_id', $event->confirmation_id)
            ->where('client_id', $event->client_id)
            ->where('pack_id', $event->pack_id)
            ->exists()
        ) {
            return $responseService->generateResponseJson(
                'error',
                404,
                'the record is already exists in database',
            );
        }

        // try to store in the database
        try{
            // store the event in table (event)
            $event->save();

            // attachement ave les tables pivot
            foreach($eventRequest->service_id as $serviceId)
                $serviceToAttach[$serviceId] = ['created_by' => $jWTService->getIdUserToken()];
            $event->services()->attach($serviceToAttach);
            foreach($eventRequest->equipement_id as $equipId)
                $equipToAttach[$equipId] = ['created_by' => $jWTService->getIdUserToken()];
            $event->equipements()->attach($equipToAttach);
            foreach($eventRequest->task_id as $taskId)
                $taskToAttach[$taskId] = ['created_by' => $jWTService->getIdUserToken()];
            $event->tasks()->attach($taskToAttach);

            // creation au niveau de table en relation hasOne (one to one)
            if($eventRequest->budget_creation)
            {
                Budget::create([
                    'event_id' => $event->id,
                    'amount' => $eventRequest->budget_amount,
                    'infos' => $eventRequest->budget_infos,
                    'created_by' => $jWTService->getIdUserToken()
                ]);
            }
            if($eventRequest->invoice_creation)
            {
                Invoice::create([
                    'event_id' => $event->id,
                    'reference' => $eventRequest->budget_amount,
                    'infos' => $eventRequest->budget_infos,
                    'created_by' => $jWTService->getIdUserToken()
                ]);
            }

            // renvoie de la reponse
            return $responseService->generateResponseJson(
                'success',
                200,
                'Data succeffully stored'
            );

        }catch(Exception $e)
        {
             // send the success response
            return $responseService->generateResponseJson(
                'error',
                500,
                'error server to saved the new event in database',
                [$e]
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(
        ResponseService $responseService,
        JWTService $jWTService,
        AboutRole $aboutRole,
        AboutUser $aboutUser,
        Event $event,
    ) {

        // données concernant l'user
        $userId = $jWTService->getIdUserToken();
        $userRoles = $aboutUser->idUserRoles($userId);
        $userServices = $aboutUser->idUserServices($userId);

        // recuperer les données
        $datas = [
            'event' => $event->toArray(),
            'services' => $event->services()->get(['service_id', 'name', 'infos'])->toArray(),
            'category' =>$event->category()->get(['id', 'name', 'infos'])->toArray(),
            'place' =>$event->place()->get(['id', 'name', 'infos'])->toArray(),
            'client' =>$event->client()->get(['id', 'name', 'infos'])->toArray(),
            'confirmation' =>$event->confirmation(['id', 'name', 'infos'])->get()->toArray(),
            'type' =>$event->type()->get(['id', 'name', 'infos'])->toArray(),
            'tasks' => $event->tasks()
                ->wherePivot('attribute_to', '=', $userId)
                ->withPivot('expiration', 'check', 'attribute_to')
                ->get()->toArray()
        ];

        if (in_array($aboutRole->idRoleAdmin(), $userRoles)) {
            $datas['budget'] = $event->budget()->get()->toArray();
            $datas['equipements'] =$event->equipements()->get()->toArray();
            $datas['tasks'] = $event->tasks()->get()->toArray();
            $datas['pack'] = $event->pack()->get()->toArray();
        } else {
            if (in_array($aboutRole->idRoleEventManager(), $userRoles)) {
                // if (audience = true OR created_by = id_user)
                if(($event->created_by !== $userId || !$event->audience))
                    $datas = [];
                else 
                    $datas['pack'] = $event->pack()->get()->toArray();

            } else {
                $eventServices = $event->services()->get(['service_id'])->toArray();
                foreach($eventServices as $eventService)
                    $services[] = $eventService['service_id'];
        
                $access = false;
                foreach($services as $service){
                    if(in_array($service, $userServices))
                    {
                        $access = true; 
                        break;
                    }
                }
                // if (audience = true AND service = user_services)
                if(!($access && $event->audience))
                    $datas = [];
            }
        }

        // send the response
        return $responseService->generateResponseJson(
            'success',
            200,
            'Event successfully showed',
            $datas
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        ResponseService $responseService,
        EventRequest $eventRequest,
        JWTService $jWTService,
        VoteService $permission,
        Event $event
    ) {
        $attribute = ['interact'];
        if (!$permission->resultVote($attribute, $event, $jWTService)) {
            // send the response error
            return $responseService->generateResponseJson(
                'error',
                404,
                'Not Authorized'
            );
        };

        // update the datas
        $event->date = $eventRequest->date;
        $event->audience = $eventRequest->audience;

        $event->client_id = $eventRequest->client_id;
        $event->place_id = $eventRequest->place_id;
        $event->category_id = $eventRequest->category_id;
        $event->type_id = $eventRequest->type_id;
        $event->confirmation_id = $eventRequest->confirmation_id;
        $event->pack_id = $eventRequest->pack_id;

        $event->updated_by = $jWTService->getIdUserToken();

        // verifie the unique record in database
        if (
            Event::where('date', $event->date)
            ->where('category_id', $event->category_id)
            ->where('place_id', $event->place_id)
            ->where('type_id', $event->type_id)
            ->where('confirmation_id', $event->confirmation_id)
            ->where('client_id', $event->client_id)
            ->where('pack_id', $event->pack_id)
            ->exists()
        ) {
            return $responseService->generateResponseJson(
                'error',
                404,
                'the record is already exists in database',
            );
        }

        // try to store in the database
        try{
            // store the event in table (event)
            $event->update();

            // attachement/synchronisation des tables pivot (many to many)
            $event->services()->syncWithPivotValues($eventRequest->service_id, ['updated_by' => $jWTService->getIdUserToken()]);
            $event->equipements()->syncWithPivotValues($eventRequest->equipement_id, ['updated_by' => $jWTService->getIdUserToken()]);
            $event->tasks()->syncWithPivotValues($eventRequest->task_id, ['updated_by' => $jWTService->getIdUserToken()]);

            // creation/modification des tables relation hasOne (one to one)
            if($eventRequest->budget_creation)
            {
                if($event->budget()->exists())
                    $event->budget()->update([
                        'event_id' => $event->id,
                        'amount' => $eventRequest->budget_amount,
                        'infos' => $eventRequest->budget_infos,
                        'updated_by' => $jWTService->getIdUserToken()
                    ]);
                else
                    Budget::create([
                        'event_id' => $event->id,
                        'amount' => $eventRequest->budget_amount,
                        'infos' => $eventRequest->budget_infos,
                        'created_by' => $jWTService->getIdUserToken()
                    ]);
            } else{
                if($event->budget()->exists())
                    $event->budget()->delete();
            }
            if($eventRequest->invoice_creation)
            {
                if($event->invoice()->exists())
                    $event->invoice()->update([
                        'event_id' => $event->id,
                        'reference' => $eventRequest->invoice_reference,
                        'infos' => $eventRequest->invoice_infos,
                        'updated_by' => $jWTService->getIdUserToken()
                    ]);
                else
                    Invoice::create([
                        'event_id' => $event->id,
                        'reference' => $eventRequest->invoice_reference,
                        'infos' => $eventRequest->invoice_infos,
                        'created_by' => $jWTService->getIdUserToken()
                    ]);
            } else{
                if($event->invoice()->exists())
                    $event->invoice()->delete();
            }

            // envoie de la reponse
            return $responseService->generateResponseJson(
                'success',
                200,
                'Data succeffully updated'
            );

        }catch(Exception $e)
        {
             // send the success response
            return $responseService->generateResponseJson(
                'error',
                500,
                'error server to saved the new event in database',
                [$e]
            );
        }
    }

    public function edit(
        ResponseService $responseService,
        JWTService $jWTService,
        VoteService $permission,
        Event $event
    )
    {
        // verify the permission
        $attribute = ['interact'];
        if (!$permission->resultVote($attribute, $event, $jWTService)) {
            // send the response error
            return $responseService->generateResponseJson(
            'error',
            404,
            'Not Authorized'
            );
        };

        dd($event->budget()->get()->toArray());

        // get the datas
         $datas = [
            'services' => Service::all(['id', 'name', 'infos'])->toArray(),
            'clients' => Client::all(['id', 'name', 'infos'])->toArray(),
            'types' => Type::all(['id', 'name', 'infos'])->toArray(),
            'confirmations' => Confirmation::all(['id', 'name', 'infos'])->toArray(),
            'categories' => Category::all(['id', 'name', 'infos'])->toArray(),
            'places' => Place::all(['id', 'name', 'infos'])->toArray(),
            'packs' => Pack::all(['id', 'name', 'infos'])->toArray()
        ];

        return $responseService->generateResponseJson(
            'success',
            200,
            'Data succeffully getted',
            $datas
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        ResponseService $responseService,
        JWTService $jWTService,
        VoteService $permission,
        Event $event
    ) {
        // verify the permission
        $attribute = ['interact'];
        if (!$permission->resultVote($attribute, $event, $jWTService)) {
            // send the response error
            return $responseService->generateResponseJson(
                'error',
                404,
                'Not Authorized'
            );
        };

        $event->delete();
        return $responseService->generateResponseJson(
            'success',
            200,
            'Event successfully deleted'
        );
    }
}
