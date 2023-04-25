<?php

namespace App\Helpers;

use App\Http\Requests\RegisterJournalRequest;
use App\Http\Requests\StoreAssetRequest;
use App\Http\Requests\SyncAmountAssetRequest;
use App\Models\Asset;
use App\Models\Event;
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
        AboutUser $aboutUser,
    )
    {

        $asset = new Asset();
        $asset->amount = $registerJournalRequest->amount;
        $asset->money_id = $registerJournalRequest->money_id;
        $asset->event_id = $registerJournalRequest->event_id;
        $asset->created_by = $aboutUser->id();

        if($asset->save())
            return true;

        return false;
    }

    /**
     * synchonise the amount in asset for the new journal(debit or credit)
     * @return true|responseService
     */
    public function syncAmount(
        ResponseService $responseService,
        RegisterJournalRequest $registerJournalRequest,
        AboutUser $aboutUser,
        Event $event
    )
    {
        $assets = $event->assets()
            ->where('money_id', $registerJournalRequest->money_id);

        if(!$assets->exists())
        {
            $asset = $this->store($registerJournalRequest, $aboutUser);
        }

        if(!$registerJournalRequest->debit)
            $registerJournalRequest->amount  *= (-1);

        foreach($assets->get() as $asset)
        {
            $asset->update([
                'amount' => $asset->amount + $registerJournalRequest->amount,
                'updated_by' => $aboutUser->id()
            ]);
        };

        return true;

    }

}