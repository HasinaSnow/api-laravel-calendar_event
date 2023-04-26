<?php

namespace App\Helpers;

use App\Http\Requests\RegisterJournalRequest;
use App\Http\Requests\StoreAssetRequest;
use App\Http\Requests\SyncAmountAssetRequest;
use App\Models\Asset;
use App\Models\Event;
use App\Models\Journal;
use App\Services\Response\ResponseService;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Test\Constraint\ResponseFormatSame;

class AboutAsset
{

    /**
     * store in the asset table
     * @return bool
     */
    public function store(
        RegisterJournalRequest $registerJournalRequest,
        AboutCurrentUser $aboutCurrentUser,
        Event $event
    )
    {

        $asset = new Asset();
        $asset->amount = $registerJournalRequest->amount;
        $asset->money_id = $registerJournalRequest->money_id;
        $asset->event_id = $event->id;
        $asset->created_by = $aboutCurrentUser->id();

        if($asset->save())
            return true;

        return false;
    }

    /**
     * synchonise the amount in asset for the new journal(debit or credit)
     * @return true|responseService
     */
    public function syncAmount(
        RegisterJournalRequest $registerJournalRequest,
        AboutCurrentUser $aboutCurrentUser,
        Event $event
    )
    {
        $assets = $event->assets()
            ->where('money_id', $registerJournalRequest->money_id);

        if(!$registerJournalRequest->debit)
            $registerJournalRequest->amount  *= (-1);

        if(!$assets->exists())
        {
            $asset = $this->store($registerJournalRequest, $aboutCurrentUser, $event);
        } else
        {
            foreach($assets->get() as $asset)
            {
                $asset->update([
                    'amount' => $asset->amount + $registerJournalRequest->amount,
                    'updated_by' => $aboutCurrentUser->id()
                ]);
            };
        }

        return true;

    }

    public function recalculateAsset(
        Event $event,
        Journal $journal,
        AboutCurrentUser $aboutCurrentUser
    )
    {
        // dd($journal->money_id);
        $assets = $event->assets()
            ->where('money_id', $journal->money_id);

        if($journal->debit)
            $journal->amount = $journal->amount * (-1);

        foreach($assets->get() as $asset)
            {          
                $asset->update([
                    'amount' => $asset->amount + $journal->amount,
                    'updated_by' => $aboutCurrentUser->id()
                ]);
            };
        
    }

}