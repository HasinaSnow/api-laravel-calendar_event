<?php

namespace App\Http\Controllers;

use App\Helpers\AboutUser;
use App\Models\Equipement;
use App\Models\Event;
use App\Services\Response\ResponseService;

class EventEquipementJournalController extends Controller
{
    public function indexEquipementJournals(
        ResponseService $responseService,
        AboutUser $aboutUser,
        Event $event,
        Equipement $equipement
    ) {
        if (!$aboutUser->isAdmin())
            return $responseService->notAuthorized();

        $journals = $equipement->journals()
            ->where('event_id', $event->id)
            ->get();

        return $responseService->successfullGetted($journals->toArray(), 'Journals');
    }
}
