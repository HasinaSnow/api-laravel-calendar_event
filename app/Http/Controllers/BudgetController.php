<?php

namespace App\Http\Controllers;

use App\Http\Requests\BudgetRequest;
use App\Models\Budget;
use App\Services\JWT\JWTService;
use App\Services\Permission\Voter\VoteService;
use App\Services\Response\ResponseService;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(
        JWTService $jWTService,
        ResponseService $responseService,
        VoteService $permission,
        Budget $budget
    )
    {
        $attribute = [];

        if (!$permission->resultVote($attribute, $budget, $jWTService)) {
            // send the response error
            return $responseService->generateResponseJson(
                'error',
                404,
                'Not Authorized'
            );
        };

        // get all clients in the database
        $budgets = Budget::orderby('id', 'desc')->get()->toArray();

        // send the response
        return $responseService->generateResponseJson(
            'success',
            200,
            'All events successfully getted',
            $budgets
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(
        ResponseService $responseService,
        JWTService $jWTService,
        VoteService $permission,
        BudgetRequest $budgetRequest,
        Budget $budget
    )
    {
        $attribute = ['create'];

        if (!$permission->resultVote($attribute, $budget, $jWTService)) {
            // send the response error
            return $responseService->generateResponseJson(
                'error',
                404,
                'Not Authorized'
            );
        };

        // store in the database
        $budget = new Budget();
        $budget->amount = $budgetRequest->amount;
        $budget->infos = $budgetRequest->infos;
        $budget->deposit_id = $budgetRequest->deposit_id;
        $budget->payment_id = $budgetRequest->payment_id;
        
        $budget->created_by = $jWTService->getIdUserToken();

        if ($budget->save());

        // send the response
        return $responseService->generateResponseJson(
            'success',
            200,
            'Budget successfully saved',
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(
        ResponseService $responseService,
        JWTService $jWTService,
        VoteService $permission,
        Budget $budget
    )
    {
        $attribute = ['interact'];
        if (!$permission->resultVote($attribute, $budget, $jWTService)) {
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
            'Budget successfully showed',
            $budget->toArray()
        );
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        ResponseService $responseService,
        BudgetRequest $budgetRequest,
        JWTService $jWTService,
        VoteService $permission,
        Budget $budget
    )
    {
        $attribute = ['interact'];

        if (!$permission->resultVote($attribute, $budget, $jWTService)) {
            // send the response error
            return $responseService->generateResponseJson(
                'error',
                404,
                'Not Authorized'
            );
        };

        // registered the data 
        $budget->amount = $budgetRequest->amount;
        $budget->infos = $budgetRequest->infos;
        $budget->deposit_id = $budgetRequest->deposit_id;
        $budget->payment_id = $budgetRequest->payment_id;
        $budget->updated_by = $jWTService->getIdUserToken();

        if ($budget->save());
            // send the response
            return $responseService->generateResponseJson(
                'success',
                200,
                'budget successfully updated',
            );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        ResponseService $responseService,
        JWTService $jWTService,
        VoteService $permission,
        Budget $budget
    )
    {
        $attribute = ['interact'];

        if (!$permission->resultVote($attribute, $budget, $jWTService)) {
            // send the response error
            return $responseService->generateResponseJson(
                'error',
                404,
                'Not Authorized'
            );
        };

        $budget->delete();
        return $responseService->generateResponseJson(
            'succes',
            200,
            'Budget successfully deleted'
        );
    }
}
