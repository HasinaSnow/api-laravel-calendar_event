<?php

namespace App\Http\Controllers;

use App\Helpers\AboutCurrentUser;
use App\Http\Requests\BudgetRequest;
use App\Http\Requests\UpdateBudgetRequest;
use App\Models\Budget;
use App\Services\Response\ResponseService;

class BudgetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(
        ResponseService $responseService,
        AboutCurrentUser $aboutCurrentUser
    )
    {
        // verify the permission
        if(!$aboutCurrentUser->isAdmin())
            return $responseService->notAuthorized();

        // get all budget in the database
        $budgets = Budget::orderby('id', 'desc')
            ->with(['event:id,date', 'payments'])
            ->get()->toArray();

        // send the response
        return $responseService->successfullGetted($budgets, 'All Budgets');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(
        ResponseService $responseService,
        AboutCurrentUser $aboutCurrentUser,
        BudgetRequest $budgetRequest,
    )
    {
        // verify the permission
        if(!$aboutCurrentUser->isAdmin())
            return $responseService->notAuthorized();

        if(Budget::where('event_id', $budgetRequest->event_id)->exists())
            return $responseService->errorServer();

        // store in the database
        $budget = new Budget();
        $budget->amount = $budgetRequest->amount;
        $budget->infos = $budgetRequest->infos;
        $budget->event_id = $budgetRequest->event_id;
        $budget->created_by = $aboutCurrentUser->id();

        if ($budget->save())
            return $responseService->successfullStored('Budget');

        return $responseService->errorServer();
    }

    /**
     * Display the specified resource.
     */
    public function show(
        ResponseService $responseService,
        AboutCurrentUser $aboutCurrentUser,
        Budget $budget
    )
    {
        // verify the permission
        if(!$aboutCurrentUser->isAdmin())
            return $responseService->notAuthorized();
        
        $datas = [
            'budget' => $budget,
            'event' => $budget->event()->get(),
            'payments' => $budget->payments()->get()
        ];

        // send the response
        return $responseService->successfullGetted($datas, 'Budget');
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        ResponseService $responseService,
        AboutCurrentUser $aboutCurrentUser,
        UpdateBudgetRequest $updateBudgetRequest,
        Budget $budget
    )
    {
        // verify the permission
        if(!$aboutCurrentUser->isAdmin())
            return $responseService->notAuthorized();

        // update the data 
        $budget->amount = $updateBudgetRequest->amount;
        $budget->infos = $updateBudgetRequest->infos;
        $budget->updated_by = $aboutCurrentUser->id();

        if ($budget->update());
            return $responseService->successfullUpdated('Budget');

        return $responseService->errorServer();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        ResponseService $responseService,
        AboutCurrentUser $aboutCurrentUser,
        Budget $budget
    )
    {
        // verify the permission
        if(!$aboutCurrentUser->isAdmin())
            return $responseService->notAuthorized();

        if($budget->delete())
            return $responseService->successfullDeleted('Budget');

        return $responseService->errorServer();
    }
}
