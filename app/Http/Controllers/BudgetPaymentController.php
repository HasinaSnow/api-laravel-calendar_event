<?php

namespace App\Http\Controllers;

use App\Helpers\AboutUser;
use App\Http\Requests\checkPaidRequest;
use App\Http\Requests\InitializePaymentRequest;
use App\Models\Budget;
use App\Models\Deposit;
use App\Models\Payment;
use App\Models\Remainder;
use App\Services\Response\ResponseService;
use Illuminate\Http\Client\Events\ResponseReceived;
use Illuminate\Http\Request;

class BudgetPaymentController extends Controller
{
    /**
     * initialize payment of the specified budget
     */
    public function initializePayment(
        ResponseService $responseService,
        InitializePaymentRequest $initializePaymentRequest,
        AboutUser $aboutUser,
        Budget $budget
    )
    {
        // verify the permission
        if(!$aboutUser->isAdmin())
            return $responseService->notAuthorized();

        // if the payment is already initialized
        if($budget->payments()->exists())
            return $responseService->generateResponseJson(
                'error',
                500,
                'A payment has already initialized for this budget'
            );

        // initialize payment for remainder
        $payment_remainder = new Payment();
        $payment_remainder->budget_id = $budget->id;
        $payment_remainder->paid = false;
        $payment_remainder->paid_at = null;
        $payment_remainder->created_by = $aboutUser->id();

        // initialize a remainder
        $remainder = new Remainder();
        $remainder->expiration = $initializePaymentRequest->remainder['expiration'];
        $remainder->infos = $initializePaymentRequest->remainder['infos'];
        $remainder->created_by = $aboutUser->id();

        // if deposit not required
        if(!$initializePaymentRequest->deposit_initialized)
        {
            $remainder->rate = 100 ;
            $remainder->amount = $budget->amount;
        }
        else
        {
            // initialize payment for deposit
            $payment_deposit = new Payment();
            $payment_deposit->budget_id = $budget->id;
            $payment_deposit->paid = false;
            $payment_deposit->paid_at = null;
            $payment_deposit->created_by = $aboutUser->id();

            // initialize a deposit
            $deposit = new Deposit();
            $deposit->expiration = ($initializePaymentRequest->deposit['expiration']) ?? null;
            $deposit->infos = $initializePaymentRequest->deposit['infos'];
            $deposit->created_by = $aboutUser->id();

            if($initializePaymentRequest->deposit['rate'])
            {
                $deposit->rate = $initializePaymentRequest->deposit['rate'];
                $deposit->amount = ($initializePaymentRequest->deposit['rate'] * $budget->amount) / 100;
            } else {
                $deposit->amount = $initializePaymentRequest->deposit['amount'];
                $deposit->rate = ($initializePaymentRequest->deposit['amount'] * 100) / $budget->amount;
            }

            $deposit->save();
            $deposit->payment()->save($payment_deposit);
            $deposit->refresh();

            $remainder->rate = 100 - $deposit->rate;
            $remainder->amount = $budget->amount - $deposit->amount;

        }

        $remainder->save();
        $remainder->payment()->save($payment_remainder);

        return $responseService->successfullStored('Payment');

    }

    /**
     * remove all the payments(remainder, deposit) belongs to the specific budget
     */
    public function removePayments(
        ResponseService $responseService,
        AboutUser $aboutUser,
        Budget $budget
    )
    {
        // verify the permission
        if(!$aboutUser->isAdmin())
            return $responseService->notAuthorized();
        
        $payments = Payment::where('budget_id', $budget->id);

        if(!$payments->exists())
            return $responseService->notExists();

        foreach($payments->get() as $payment)
        {
            $payment->paymentable_type::find($payment->paymentable_id)->delete();
            $payment->delete();
        }
    
        return $responseService->successfullDeleted('Payments');

    }

    /**
     * remove deposit payment and recalculate the remainder payment
     */
    public function removePaymentDeposit(
        ResponseService $responseService,
        AboutUser $aboutUser,
        Budget $budget
    )
    {
        // verify the permission
        if(!$aboutUser->isAdmin())
            return $responseService->notAuthorized();

        $payments = Payment::where('budget_id', $budget->id);
        // dd($payments->get()[0]);

        if(!$payments->exists())
            return $responseService->notExists('Payment');

        // dd($payments->deposit()->get());
        foreach($payments->get() as $payment)
        {
            if($payment->paymentable_type === "App\Models\Deposit")
            {
                dd($payment->get()[0]->deposit);
            }
        }
    }

}
