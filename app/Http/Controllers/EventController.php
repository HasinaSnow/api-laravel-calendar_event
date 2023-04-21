<?php

namespace App\Http\Controllers;

use App\Helpers\AboutUser;
use App\Http\Requests\AttachTaskListRequest;
use App\Http\Requests\AttributeTasklistRequest;
use App\Http\Requests\DetachTaskListRequest;
use App\Http\Requests\EventRequest;
use App\Http\Requests\ExpirationTaskListRequest;
use App\Http\Requests\TaskAttributeRequest;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Client;
use App\Models\Confirmation;
use App\Models\Event;
use App\Models\Invoice;
use App\Models\Pack;
use App\Models\Place;
use App\Models\Service;
use App\Models\Task;
use App\Models\Type;
use App\Models\User;
use App\Services\Response\ResponseService;

class EventController extends Controller
{
    /**
     * Display a listing of the events.
     */
    public function index(
        ResponseService $responseService,
        AboutUser $aboutUser,
    ) 
    {

        // get the data in db
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
        // trier les données en fonction du profile de l'user
        if ($aboutUser->isAdmin()) {
            $events = $events->with('budget:event_id,amount', 'pack:id,name');
        } else {
            if ($aboutUser->isEventManager()) {
                $events = $events
                    ->with('pack:id,name')
                    ->where('audience', true) 
                    ->orWhere('created_by', $aboutUser->id());
            } else {
                $events = $events
                    ->where('audience', true)
                    ->whereRelation('services', 'service_id', '=', implode('||', $aboutUser->idServices()));
            }
        }
        // send the success response
        return $responseService->successfullGetted($events->get()->toArray(), 'All events');
    }

    /**
     * Display a listing of the event to store a new data
     */
    public function create(
        ResponseService $responseService,
        AboutUser $aboutUser,
        Event $event
    )
    {
        // verify the user permission 
        if (!$aboutUser->isPermisToCreate($event))
            return $responseService->notAuthorized();
        // get the data in db
        $datas = [
            'services' => Service::all(['id', 'name', 'infos'])->toArray(),
            'clients' => Client::all(['id', 'name', 'infos'])->toArray(),
            'types' => Type::all(['id', 'name', 'infos'])->toArray(),
            'confirmations' => Confirmation::all(['id', 'name', 'infos'])->toArray(),
            'categories' => Category::all(['id', 'name', 'infos'])->toArray(),
            'places' => Place::all(['id', 'name', 'infos'])->toArray(),
            'packs' => Pack::all(['id', 'name', 'infos'])->toArray()
        ];
        // send the success response
        return $responseService->successfullGetted($datas);
    }

    /**
     * Store a newly created event in storage.
     */
    public function store(
        ResponseService $responseService,
        EventRequest $eventRequest,
        AboutUser $aboutUser,
        Event $event
    ) 
    {
        // verify the user permission
        if(!$aboutUser->isPermisToCreate($event))
            return $responseService->notAuthorized();

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
        $event->created_by = $aboutUser->id();

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
        ) 
            return $responseService->alreadyExist('Event');

        // store the event
        $event->save();

        // attachement avec les tables pivot (many to many)
        foreach($eventRequest->service_id as $serviceId)
            $serviceToAttach[$serviceId] = ['created_by' => $aboutUser->id()];
        $event->services()->attach($serviceToAttach);

        // creation au niveau de table en relation hasOne (one to one)
        if($eventRequest->budget_creation)
        {
            Budget::create([
                'event_id' => $event->id,
                'amount' => $eventRequest->budget_amount,
                'infos' => $eventRequest->budget_infos,
                'created_by' => $aboutUser->id()
            ]);
        }

        // creation automatique d'un invoice
        Invoice::create([
            'event_id' => $event->id,
            'reference' => "invoice_" . $event->id,
            'created_by' => $aboutUser->id()
        ]);

        // send the successfull response
        return $responseService->successfullStored('Event');

    }

    /**
     * Attach the list of task in the specified event
     */
    public function attachTaskList(
        ResponseService $responseService,
        AttachTaskListRequest $attachTaskListRequest,
        AboutUser $aboutUser,
        Event $event,
    )
    {
        if(!($aboutUser->isAdmin() || $aboutUser->isTaskManager()))
            return $responseService->notAuthorized();

        // dd(count($attachTaskListRequest->tasks));
        
        // list of tasks 
        foreach($attachTaskListRequest->tasks as $task)
        {
            $serviceToAttach[$task['id']] = [
                'created_by' => $aboutUser->id(),
                'expiration' => $task['expiration'],
            ];
            if(isset($task['attribute_to']))
            {
                $serviceToAttach[$task['id']]['attribute_to'] = $task['attribute_to'];
                $serviceToAttach[$task['id']]['attribute_at'] = now();
            }
            if(isset($task['check']))
            {
                $serviceToAttach[$task['id']]['check'] = $task['check'];
                $serviceToAttach[$task['id']]['check_at'] = now();
            }
            if(isset($task['expiration']))
                $serviceToAttach[$task['id']]['expiration'] = $task['expiration'];
        }

        // attach all the list of tasks
        if($event->tasks()->syncWithoutDetaching($serviceToAttach))
            return $responseService->successfullAttached(
                count($attachTaskListRequest->tasks) . ' Task(s)', 
                $event->date
            );
        
        // send error serv
        return $responseService->errorServer();
    }

    /**
     * Detach the list of tasks in the specified event
     */
    public function detachTaskList(
        ResponseService $responseService,
        DetachTaskListRequest $detachTaskListRequest,
        AboutUser $aboutUser,
        Event $event,
    )
    {
        if(!($aboutUser->isAdmin() || $aboutUser->isTaskManager()))
            return $responseService->notAuthorized();

        if($event->tasks()->detach($detachTaskListRequest->tasks))
            return $responseService->successfullDetached(
                count($detachTaskListRequest->tasks) . ' Task(s)', 
                $event->date
            );
        
        return $responseService->errorServer();
    }

    /**
     * Display the specified event.
     */
    public function show(
        ResponseService $responseService,
        AboutUser $aboutUser,
        Event $event,
    ) 
    {
        // recuperer les données
        $datas = [
            'event' => $event,
            'services' => $event->services()->get(['service_id', 'name']),
            'category' =>$event->category()->get(['id', 'name', 'infos']),
            'place' =>$event->place()->get(['id', 'name', 'infos']),
            'client' =>$event->client()->get(['id', 'name', 'infos']),
            'confirmation' =>$event->confirmation(['id', 'name', 'infos']),
            'type' =>$event->type()->get(['id', 'name', 'infos']),
        ]; 

        // 
        if ($aboutUser->isAdmin()) {
            $datas['pack'] = $event->pack()->get()->toArray();
            $datas['budget'] = $event->budget()->get()->toArray();
            $datas['equipements'] =$event->equipements()->get()->toArray();
            $datas['tasks'] = $event->tasks()->get()->toArray();
        } else {
            if ($aboutUser->isEventManager()) {
                // if (audience = true OR created_by = id_user)
                if(!($aboutUser->isCreator($event) || $event->audience))
                    $datas = [];
                else 
                    $datas['pack'] = $event->pack()->get()->toArray();

            } else {
                $eventServices = $event->services()->get(['service_id'])->toArray();
                foreach($eventServices as $eventService)
                    $services[] = $eventService['service_id'];
        
                $access = false;
                foreach($services as $service){
                    if(in_array($service, $aboutUser->idServices()))
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
     * Display the tasks attributed to the specified event.
     */
    public function showTaskList(
        ResponseService $responseService,
        AboutUser $aboutUser,
        Event $event
    )
    {
        // verify the permission
        if($aboutUser->isAdmin() || $aboutUser->isTaskManager())
            $datas = $event->tasks()->get()->toArray();
        else {
            $datas = $event->tasks()
                ->wherePivot('attribute_to', '=', $aboutUser->id())
                ->withPivot('expiration', 'check', 'attribute_to')
                ->get()->toArray();
        }

        return $responseService->successfullGetted($datas, 'Tasks of event');
            
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        ResponseService $responseService,
        EventRequest $eventRequest,
        AboutUser $aboutUser,
        Event $event
    ) 
    {
        if (!$aboutUser->isPermisToInteract($event))
            return $responseService->notAuthorized();

        // update the datas
        $event->date = $eventRequest->date;
        $event->audience = $eventRequest->audience;

        $event->client_id = $eventRequest->client_id;
        $event->place_id = $eventRequest->place_id;
        $event->category_id = $eventRequest->category_id;
        $event->type_id = $eventRequest->type_id;
        $event->confirmation_id = $eventRequest->confirmation_id;
        $event->pack_id = $eventRequest->pack_id;

        $event->updated_by = $aboutUser->id();

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
            return $responseService->alreadyExist('Event');
        }

        // store the event in table (event)
        $event->update();

        // attachement/synchronisation des tables pivot (many to many)
        $event->services()->syncWithPivotValues($eventRequest->service_id, ['updated_by' => $aboutUser->id()]);

        // creation/modification des tables relation hasOne (one to one)
        if($eventRequest->budget_creation)
        {
            if($event->budget()->exists())
                $event->budget()->update([
                    'event_id' => $event->id,
                    'amount' => $eventRequest->budget_amount,
                    'infos' => $eventRequest->budget_infos,
                    'updated_by' => $aboutUser->id()
                ]);
            else
                Budget::create([
                    'event_id' => $event->id,
                    'amount' => $eventRequest->budget_amount,
                    'infos' => $eventRequest->budget_infos,
                    'created_by' => $aboutUser->id()
                ]);
        } else{
            if($event->budget()->exists())
                $event->budget()->delete();
        }

        // envoie de la reponse
        return $responseService->successfullUpdated('Event');

    }

    /**
     * Attribute tasks to specified user
     */
    public function attributeTask(
        ResponseService $responseService,
        AboutUser $aboutUser,
        TaskAttributeRequest $taskAttributeRequest,
        Event $event,
        Task $task
    )
    {

        // verify the permission who attribute the task (admin, taskManager)
        if(!$aboutUser->isPermisToInteract($task))
            return $responseService->notAuthorized();

        // verify the id user who we attribute the task is in service of the task
        if(!in_array($task->service_id, $aboutUser->idServicesOfUserSpecified($taskAttributeRequest->attribute_to)))
            return $responseService->generateResponseJson('error', 500, 'Not authorized to attribute this task for this user');
            
        $event->tasks()->updateExistingPivot($task->id, [
            'attribute_to' => $taskAttributeRequest->attribute_to,
            'attribute_at' => now(),
            'updated_by' => $aboutUser->id(),
            'updated_at' => now()
        ]);
        return $responseService->generateResponseJson(
            'success',
            200,
            "Task : '" . $task->name . "' attributed to '" . User::findOrFail($taskAttributeRequest->attribute_to)->name . "'."
        );
        
    }

    /**
     * attribute a list of task to a specific user in service
     */
    public function attributeTaskList(
        ResponseService $responseService,
        AttributeTasklistRequest $attributeTasklistRequest,
        AboutUser $aboutUser,
        Event $event,
    )
    {
        if(!($aboutUser->isAdmin() || $aboutUser->isTaskManager()))
            return $responseService->notAuthorized();

        // les tasks doivent être de même services
        // return $responseService->generateResponseJson('error', 500, 'Not authorized to attribute this task for this user');

        $data = [
            'attribute_to' => $attributeTasklistRequest->attribute_to,
            'attribute_at' => now(),
            'updated_by' => $aboutUser->id(),
            'updated_at' => now()
        ];

        if(isset($attributeTasklistRequest['expiration']))
            $data['expiration'] = $attributeTasklistRequest->expiration;

        if(
            $event->tasks()->updateExistingPivot($attributeTasklistRequest->tasks, $data)
        )
            return $responseService->successfullUpdated('List of Task');

        // send error serv
        return $responseService->errorServer();
    }

    /**
     * update the expiration of the taskList specified
     */
    public function expirationTaskList(
        ResponseService $responseService,
        ExpirationTaskListRequest $expirationTaskListRequest,
        AboutUser $aboutUser,
        Event $event
    )
    {
        if(!($aboutUser->isAdmin() || $aboutUser->isTaskManager()))
            return $responseService->notAuthorized();

        $data = [
            'expiration' => $expirationTaskListRequest->expiration,
            'updated_by' => $aboutUser->id(),
            'updated_at' => now()
        ];

        if(
            $event->tasks()->updateExistingPivot($expirationTaskListRequest->tasks, $data)
        )
            return $responseService->successfullUpdated('List of Task');

        // send error serv
        return $responseService->errorServer();
    }

    /**
     * check the tasks for the specified event
     */
    public function checkTask(
        ResponseService $responseService,
        AboutUser $aboutUser,
        Event $event,
        Task $task
    )
    {
        // verify the user who checked the task
        if($aboutUser->isAdmin() || $event->tasks()->findOrFail($task->id)->toArray()['pivot']['attribute_to'] === $aboutUser->id())
        {
            if($event->tasks()->findOrFail($task->id)->toArray()['pivot']['check'])
                return $responseService->generateResponseJson(
                    'success',
                    200,
                    'The task is already checked'
                );

            $event->tasks()->updateExistingPivot($task->id, [
                'check' => true,
                'check_at' => now(),
                'updated_by' => $aboutUser->id(),
                'updated_at' => now()
            ]);
            return $responseService->generateResponseJson(
                'success',
                200,
                'Task:' . $task->name . ' is successfully checked'
            );
        }
        
        return $responseService->notAuthorized();        
        
    }

    /**
     * Display a listing of the resource to update a data
     */
    public function edit(
        ResponseService $responseService,
        AboutUser $aboutUser,
        Event $event
    )
    {
        // verify the permission
        if (!$aboutUser->isPermisToCreate($event)) 
            return $responseService->notAuthorized();

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

        // return the response
        return $responseService->successfullGetted($datas);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        ResponseService $responseService,
        AboutUser $aboutUser,
        Event $event
    ) {
        // verify the permission
        if (!$aboutUser->isPermisToInteract($event))
            return $responseService->notAuthorized();

        $event->delete();
        return $responseService->successfullDeleted('Event');
    }

}
