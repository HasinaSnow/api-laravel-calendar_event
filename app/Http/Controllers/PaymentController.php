<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentRequest;
use App\Models\Payment;
use App\Services\JWT\JWTService;
use App\Services\Permission\Voter\VoteService;
use App\Services\Response\ResponseService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index( 
        ResponseService $responseService,
        JWTService $jWTService,
        VoteService $permission,
        Payment $payment
    )
    {
        // verifie the permission
        $attribute = [];
        if (!$permission->resultVote($attribute, $payment, $jWTService)) {
            // send the response error
            return $responseService->generateResponseJson(
                'error',
                404,
                'Not Authorized'
            );
        };

        // recuperer les payements créer par l'user
        $userId = $jWTService->getIdUserToken();
        $payments = Payment::orderby('id', 'desc')->get()->toArray();

        // send the response
        return $responseService->generateResponseJson(
            'success',
            200,
            'All events successfully getted',
            $payments
        );

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(
        ResponseService $responseService,
        JWTService $jWTService,
        VoteService $permission,
        Payment $payment,
        PaymentRequest $paymentRequest
    )
    {
        $attribute = ['create'];

        if (!$permission->resultVote($attribute, $payment, $jWTService)) {
            // send the response error
            return $responseService->generateResponseJson(
                'error',
                404,
                'Not Authorized'
            );
        };

        $payment = new Payment();
        $payment->type = $paymentRequest->type;
        $payment->infos = $paymentRequest->infos;
        $payment->isPaid = $paymentRequest->isPaid;
        $payment->paid_at = $paymentRequest->paid_at;

        $payment->created_by = $jWTService->getIdUserToken();

        if ($payment->save());

        // send the response
        return $responseService->generateResponseJson(
            'success',
            200,
            'Payment successfully saved',
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(
        ResponseService $responseService,
        JWTService $jWTService,
        VoteService $permission,
        Payment $payment
    )
    {
        $attribute = ['interact'];
        if (!$permission->resultVote($attribute, $payment, $jWTService)) {
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
            'Payment successfully showed',
            $payment->toArray()
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        ResponseService $responseService,
        JWTService $jWTService,
        VoteService $permission,
        Payment $payment,
        PaymentRequest $paymentRequest
    )
    {
        $attribute = ['interact'];
        if (!$permission->resultVote($attribute, $payment, $jWTService)) {
            // send the response error
            return $responseService->generateResponseJson(
                'error',
                404,
                'Not Authorized'
            );
        };

        // record the data
        $payment->type = $paymentRequest->type;
        $payment->infos = $paymentRequest->infos;
        $payment->isPaid = $paymentRequest->isPaid;
        $payment->paid_at = $paymentRequest->paid_at;

        $payment->updated_by = $jWTService->getIdUserToken();

        if ($payment->save());

        // send the response
        return $responseService->generateResponseJson(
            'success',
            200,
            'Payment successfully saved',
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        ResponseService $responseService,
        JWTService $jWTService,
        VoteService $permission,
        Payment $payment
    )
    {
        $attribute = ['interact'];

        if (!$permission->resultVote($attribute, $payment, $jWTService)) {
            // send the response error
            return $responseService->generateResponseJson(
                'error',
                404,
                'Not Authorized'
            );
        };

        $payment->delete();
        return $responseService->generateResponseJson(
            'succes',
            200,
            'Payment successfully deleted'
        );
    }
}
