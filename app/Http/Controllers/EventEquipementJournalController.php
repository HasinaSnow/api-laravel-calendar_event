<?php

namespace App\Http\Controllers;

use App\Helpers\AboutCurrentUser;
use App\Models\Equipement;
use App\Models\Event;
use App\Services\Response\ResponseService;

class EventEquipementJournalController extends Controller
{
    public function indexEquipementJournals(
        ResponseService $responseService,
        AboutCurrentUser $aboutCurrentUser,
        Event $event,
        Equipement $equipement
    ) {
        if (!$aboutCurrentUser->isAdmin())
            return $responseService->notAuthorized();

        $journals = $equipement->journals()
            ->where('event_id', $event->id)
            ->get();

        return $responseService->successfullGetted($journals->toArray(), 'Journals');
    }
}
