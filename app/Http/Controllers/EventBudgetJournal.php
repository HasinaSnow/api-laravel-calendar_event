<?php

namespace App\Http\Controllers;

use App\Helpers\AboutCurrentUser;
use App\Models\Budget;
use App\Models\Event;
use App\Services\Response\ResponseService;
use Illuminate\Http\Request;
use Psy\Command\WhereamiCommand;

class EventBudgetJournal extends Controller
{
    public function indexBudgetJournals(
        ResponseService $responseService,
        AboutCurrentUser $aboutCurrentUser,
        Event $event,
        Budget $budget
    )
    {
        if(!$aboutCurrentUser->isAdmin())
            return $responseService->notAuthorized();

        $journals = $budget->journals()
            ->where('event_id', $event->id)
            ->get();

        return $responseService->successfullGetted($journals->toArray(), 'Journals');
    }
}
