<?php

namespace App\Http\Controllers;

use App\Http\Requests\DepositRequest;
use App\Models\Deposit;
use App\Services\JWT\JWTService;
use App\Services\Permission\Voter\VoteService;
use App\Services\Response\ResponseService;

class DepositController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(
        ResponseService $responseService,
        JWTService $jWTService,
        VoteService $permission,
        Deposit $deposit
    ) {
        // verifie the permission
        $attribute = [];
        if (!$permission->resultVote($attribute, $deposit, $jWTService)) {
            // send the response error
            return $responseService->generateResponseJson(
                'error',
                404,
                'Not Authorized'
            );
        };

        // get all clients in the database
        $userId = $jWTService->getIdUserToken();
        $deposits = Deposit::where('created_by', $userId)->orderby('id', 'desc')->get()->toArray();

        dd($deposits);

        // send the response
        return $responseService->generateResponseJson(
            'success',
            200,
            'All events successfully getted',
            $deposits
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(
        ResponseService $responseService,
        JWTService $jWTService,
        VoteService $permission,
        Deposit $deposit,
        DepositRequest $depositRequest
    ) {
        $attribute = ['create'];
        if (!$permission->resultVote($attribute, $deposit, $jWTService)) {
            // send the response error
            return $responseService->generateResponseJson(
                'error',
                404,
                'Not Authorized'
            );
        };

        // store in the database
        $deposit = new Deposit();
        $deposit->amount = $depositRequest->amount;
        $deposit->expiration = $depositRequest->expiration;
        $deposit->rate = $depositRequest->rate;
        $deposit->payment_id = $depositRequest->payment_id;

        $deposit->created_by = $jWTService->getIdUserToken();

        if ($deposit->save());

        // send the response
        return $responseService->generateResponseJson(
            'success',
            200,
            'Deposit successfully saved',
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(
        ResponseService $responseService,
        JWTService $jWTService,
        VoteService $permission,
        Deposit $deposit
    ) {
        $attribute = ['interact'];
        if (!$permission->resultVote($attribute, $deposit, $jWTService)) {
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
            'Deposit successfully showed',
            $deposit->toArray()
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        ResponseService $responseService,
        JWTService $jWTService,
        VoteService $permission,
        Deposit $deposit,
        DepositRequest $depositRequest
    ) {
        $attribute = ['interact'];

        if (!$permission->resultVote($attribute, $deposit, $jWTService)) {
            // send the response error
            return $responseService->generateResponseJson(
                'error',
                404,
                'Not Authorized'
            );
        };

        // registered the data 
        $deposit->amount = $depositRequest->amount;
        $deposit->expiration = $depositRequest->expiration;
        $deposit->rate = $depositRequest->rate;
        $deposit->payment_id = $depositRequest->payment_id;

        $deposit->updated_by = $jWTService->getIdUserToken();

        if ($deposit->save());
        // send the response
        return $responseService->generateResponseJson(
            'success',
            200,
            'Deposit successfully updated',
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        ResponseService $responseService,
        JWTService $jWTService,
        VoteService $permission,
        Deposit $deposit,
    ) {
        $attribute = ['interact'];

        if (!$permission->resultVote($attribute, $deposit, $jWTService)) {
            // send the response error
            return $responseService->generateResponseJson(
                'error',
                404,
                'Not Authorized'
            );
        };

        $deposit->delete();
        return $responseService->generateResponseJson(
            'succes',
            200,
            'Deopsit successfully deleted'
        );
    }
}
