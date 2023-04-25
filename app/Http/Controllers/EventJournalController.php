<?php

namespace App\Http\Controllers;

use App\Helpers\AboutAsset;
use App\Helpers\AboutUser;
use App\Http\Requests\RegisterJournalRequest;
use App\Models\Budget;
use App\Models\Equipement;
use App\Models\Event;
use App\Models\Journal;
use App\Services\Response\ResponseService;

class EventJournalController extends Controller
{
    public function indexJournals(
        ResponseService $responseService,
        AboutUser $aboutUser,
        Event $event
    )
    {
        // verify permission
        if(!$aboutUser->isAdmin())
            return $responseService->notAuthorized();
        // get all the journals for specific event
        $journals = $event->journals()->get();

        return $responseService->successfullGetted($journals->toArray());
    }

    public function writeJournal(
        ResponseService $responseService,
        RegisterJournalRequest $registerJournalRequest,
        AboutUser $aboutUser,
        AboutAsset $aboutAsset,
        Event $event,
    )
    {
        // verify permission
        if(!$aboutUser->isAdmin())
            return $responseService->notAuthorized();

        $journal = new Journal([
            'wording' => $registerJournalRequest->wording,
            'date' => $registerJournalRequest->date,
            'debit' => $registerJournalRequest->debit,
            'amount' => $registerJournalRequest->amount,
            'money_id' => $registerJournalRequest->money_id,
            'event_id' => $event->id,
            'created_by' => $aboutUser->id()
        ]);

        if($registerJournalRequest->journal_type === 'budget')
        {
            $budget = Budget::find($registerJournalRequest->budget_id);
            $budget->journals()->save($journal);
        }
        if($registerJournalRequest->journal_type === 'equipement')
        {
            $equipement = Equipement::find($registerJournalRequest->equipement_id);
            $equipement->journals()->save($journal);
        }
        
        $aboutAsset->syncAmount($registerJournalRequest, $aboutUser, $event);
        return $responseService->successfullStored('New Journal');

    }

    /**
     * update the journal
     */
    public function rectifyJournal(
        ResponseService $responseService,
        RegisterJournalRequest $registerJournalRequest,
        AboutUser $aboutUser,
        AboutAsset $aboutAsset,
        Event $event,
        Journal $journal
    )
    {
        // verify permission
        if(!$aboutUser->isAdmin())
            return $responseService->notAuthorized();
            
        $aboutAsset->recalculateAsset($event, $journal, $aboutUser);

        $journal->update([
            'wording' => $registerJournalRequest->wording,
            'date' => $registerJournalRequest->date,
            'debit' => $registerJournalRequest->debit,
            'amount' => $registerJournalRequest->amount,
            'money_id' => $registerJournalRequest->money_id,
            'event_id' => $event->id,
            'updated_by' => $aboutUser->id()
        ]);

        $aboutAsset->syncAmount($registerJournalRequest, $aboutUser, $event);

        return $responseService->successfullUpdated('Journal');
        
    }

    /**
     * 
     */
    public function removeJournal(
        ResponseService $responseService,
        AboutUser $aboutUser,
        AboutAsset $aboutAsset,
        Event $event,
        Journal $journal
    )
    {
        // verify permission
        if(!$aboutUser->isAdmin())
            return $responseService->notAuthorized();
        
        // recalculate the last asset
        $aboutAsset->recalculateAsset($event, $journal, $aboutUser);

        // remove the journal
        $journal->delete();

        return $responseService->successfullDeleted('Journal');

    }


}
