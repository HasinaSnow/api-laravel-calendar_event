<?php

namespace App\Http\Controllers;

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
        JWTService $jWTService,
        ResponseService $responseService,
        VoteService $permission,
        Task $task
    )
    {
        $attribute = [];

        if (!$permission->resultVote($attribute, $task, $jWTService)) {
            // send the response error
            return $responseService->generateResponseJson(
                'error',
                404,
                'Not Authorized'
            );
        };

         // get all clients in the database
         $tasks = Task::orderby('id', 'desc')->get()->toArray();

         // send the response
         return $responseService->generateResponseJson(
             'success',
             200,
             'All tasks successfully getted',
             $tasks
         );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(
        ResponseService $responseService,
        JWTService $jWTService,
        VoteService $permission,
        Task $task,
        TaskRequest $taskRequest
    )
    {
        $attribute = ['create'];

        if (!$permission->resultVote($attribute, $task, $jWTService)) {
            // send the response error
            return $responseService->generateResponseJson(
                'error',
                404,
                'Not Authorized'
            );
        };

        dd($taskRequest->toArray());
         // store in the database
         $task = new Task();
         $task->name = $taskRequest->name;
         $task->infos = $taskRequest->infos;
         
         $task->created_by = $jWTService->getIdUserToken();
 
         if ($task->save());
 
         // send the response
         return $responseService->generateResponseJson(
             'success',
             200,
             'Task successfully saved',
         );
    }

    /**
     * Display the specified resource.
     */
    public function show(
        ResponseService $responseService,
        JWTService $jWTService,
        VoteService $permission,
        Task $task
    )
    {
        $attribute = ['interact'];
        if (!$permission->resultVote($attribute, $task, $jWTService)) {
            // send the response error
            return $responseService->generateResponseJson(
                'error',
                404,
                'Not Authorized'
            );
        };

        // send the response
        return $responseService->generateResponseJson(
            'success',
            200,
            'Task successfully showed',
            $task->toArray()
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        ResponseService $responseService,
        JWTService $jWTService,
        VoteService $permission,
        Task $task,
        TaskRequest $taskRequest
    )
    {
        $attribute = ['interact'];

        if (!$permission->resultVote($attribute, $task, $jWTService)) {
            // send the response error
            return $responseService->generateResponseJson(
                'error',
                404,
                'Not Authorized'
            );
        };

         // updated in the database
         $task->name = $taskRequest->name;
         $task->infos = $taskRequest->infos;
         
         $task->updated_by = $jWTService->getIdUserToken();
 
         if ($task->save());
 
         // send the response
         return $responseService->generateResponseJson(
             'success',
             200,
             'Task successfully updated',
         );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        ResponseService $responseService,
        JWTService $jWTService,
        VoteService $permission,
        Task $task
    )
    {
        $attribute = ['interact'];

        if (!$permission->resultVote($attribute, $task, $jWTService)) {
            // send the response error
            return $responseService->generateResponseJson(
                'error',
                404,
                'Not Authorized'
            );
        };

        $task->delete();
        return $responseService->generateResponseJson(
            'succes',
            200,
            'Task successfully deleted'
        );
    }
}
