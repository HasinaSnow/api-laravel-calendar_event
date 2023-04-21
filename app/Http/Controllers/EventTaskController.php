<?php

namespace App\Http\Controllers;

use App\Helpers\AboutUser;
use App\Http\Requests\AttachTaskListRequest;
use App\Http\Requests\AttributeTasklistRequest;
use App\Http\Requests\DetachTaskListRequest;
use App\Http\Requests\ExpirationTaskListRequest;
use App\Http\Requests\TaskAttributeRequest;
use App\Models\Event;
use App\Models\Task;
use App\Models\User;
use App\Services\Response\ResponseService;

class EventTaskController extends Controller
{

    /**
     * Display the tasks attributed to the specified event.
     */
    public function showTaskListAttached(
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
     * Attach the list of task in the specified event
     */
    public function attachTaskList(
        ResponseService $responseService,
        AttachTaskListRequest $attachTaskListRequest,
        AboutUser $aboutUser,
        Event $event,
    )
    {
        // verifie the permission
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
}
