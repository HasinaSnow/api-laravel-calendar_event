<?php

namespace App\Http\Controllers;

use App\Helpers\AboutCurrentUser;
use App\Http\Requests\checkPaidRequest;
use App\Http\Requests\InitializePaymentRequest;
use App\Http\Requests\PaymentRequest;
use App\Models\Budget;
use App\Models\Deposit;
use App\Models\Event;
use App\Models\Payment;
use App\Models\Remainder;
use App\Services\JWT\JWTService;
use App\Services\Permission\Voter\VoteService;
use App\Services\Response\ResponseService;

class PaymentController extends Controller
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

        // recuperer les payements créer par l'user
        $payments = Payment::orderby('id', 'desc')
            ->with([
                'paymentable:id,amount',
                'budget:id,amount,event_id',
            ])
            ->get()->toArray();

        // send the response
        return $responseService->successfullGetted($payments, 'All Payments');

    }

    /**
     * check paid the payment
     */
    public function checkPaidPayment(
        ResponseService $responseService,
        checkPaidRequest $checkPaidRequest,
        AboutCurrentUser $aboutCurrentUser,
        Budget $budget,
        Payment $payment
    )
    {
        // verify permission
        if(!$aboutCurrentUser->isAdmin())
            return $responseService->notAuthorized();

        $payment->paid = true;
        $payment->paid_at = $checkPaidRequest->paid_at;

        if($payment->update())
            return $responseService->successfullUpdated('Payment');
        
    }

    /**
     * Display the specified resource.
     */
    public function show(
        ResponseService $responseService,
        AboutCurrentUser $aboutCurrentUser,
        Payment $payment
    )
    {
        if(!$aboutCurrentUser->isAdmin())
            return $responseService->notAuthorized();

        $datas = [
            'payment' => $payment,
            'paymentable' => $payment->paymentable()->get(),
            'budget' => $payment->budget()->get()
        ];
         // send the response
         return $responseService->successfullGetted($datas, 'Payment');
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
