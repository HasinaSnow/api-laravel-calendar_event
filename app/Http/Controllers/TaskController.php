<?php

namespace App\Http\Controllers;

use App\Helpers\AboutCurrentUser;
use App\Http\Requests\TaskRequest;
use App\Models\Task;
use App\Services\JWT\JWTService;
use App\Services\Permission\Voter\VoteService;
use App\Services\Response\ResponseService;
use Illuminate\Http\Request;

class TaskController extends Controller
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
            $tasks = Task::orderby('id', 'desc')->get()->toArray();
        else if($aboutCurrentUser->isTaskManager())
            $tasks = Task::orderby('id', 'desc')
                ->whereIn('service_id', $aboutCurrentUser->idServices())
                ->with('service:id,name')
                ->get()->toArray();
        else
            return $responseService->notAuthorized();

        return $responseService->successfullGetted($tasks, 'Tasks');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(
        ResponseService $responseService,
        TaskRequest $taskRequest,
        AboutCurrentUser $aboutCurrentUser,
        Task $task
    )
    {
        // verify the permission
        if(!$aboutCurrentUser->isPermisToCreate($task))
            return $responseService->notAuthorized();

        // store in the database
        $task = new Task();
        $task->name = $taskRequest->name;
        $task->infos = $taskRequest->infos;
        $task->service_id = $taskRequest->service_id;
        $task->created_by = $aboutCurrentUser->id();

        // verify if the service_id
        if(
            !$aboutCurrentUser->isAdmin() &&
            !in_array($taskRequest->service_id, $aboutCurrentUser->idServices())
        )
            return $responseService->errorServer();

        // verify if data already exist in db
        if (
            Task::where('name', $taskRequest->name)
                ->where('service_id', $taskRequest->service_id)
                ->exists()
        ) 
            return $responseService->alreadyExist('Task');
 
        if ($task->save());
            return $responseService->successfullStored('Task');

    }

    /**
     * Display the specified resource.
     */
    public function show(
        ResponseService $responseService,
        AboutCurrentUser $aboutCurrentUser,
        Task $task
    )
    {
        $datas = [
            'task' => $task->toArray(),
            'service' => $task->service()->get()->toArray()
        ];
        // verify the permission
        if($aboutCurrentUser->isAdmin())
            return $responseService->successfullGetted($datas, 'Task');
        else if($aboutCurrentUser->isTaskManager())
            if(in_array($task->service_id, $aboutCurrentUser->idServices()))
                return $responseService->successfullGetted($datas, 'Task');

        return $responseService->notFound();

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        ResponseService $responseService,
        TaskRequest $taskRequest,
        AboutCurrentUser $aboutCurrentUser,
        Task $task
    )
    {
        if(!$aboutCurrentUser->isPermisToInteract($task))
            return $responseService->notAuthorized();

        // updated in the database
        $task->name = $taskRequest->name;
        $task->infos = $taskRequest->infos;
        $task->service_id = $taskRequest->service_id;
        $task->updated_by = $aboutCurrentUser->id();

        // verify if the service_id
        if(
            !$aboutCurrentUser->isAdmin() &&
            !in_array($taskRequest->service_id, $aboutCurrentUser->idServices())
        )
            return $responseService->errorServer();

        if (
            Task::where('name', $taskRequest->name)
                ->where('service_id', $taskRequest->service_id)
                ->exists()
        ) 
            return $responseService->alreadyExist('Task');
 
        if ($task->save())
            return $responseService->successfullUpdated('Task');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        ResponseService $responseService,
        AboutCurrentUser $aboutCurrentUser,
        Task $task
    )
    {
        if(!$aboutCurrentUser->isPermisToInteract($task))
            return $responseService->notAuthorized();

        if($task->delete())
            return $responseService->successfullDeleted('Task');
        
    }
}
