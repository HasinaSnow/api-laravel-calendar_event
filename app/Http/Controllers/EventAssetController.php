<?php

namespace App\Http\Controllers;

use App\Helpers\AboutCurrentUser;
use App\Models\Asset;
use App\Models\Event;
use App\Services\Response\ResponseService;

class EventAssetController extends Controller
{
    public function indexAssets(
        ResponseService $responseService,
        AboutCurrentUser $aboutCurrentUser,
        Event $event
    )
    {
        if(!$aboutCurrentUser->isAdmin())
            return $responseService->notAuthorized();

        $assets = Asset::where('event_id', $event->id)
        ->with(['event', 'money'])
        ->get();

        return $responseService->successfullGetted($assets->toArray(), 'Assets');
    }

    public function showAsset(
        ResponseService $responseService,
        AboutCurrentUser $aboutCurrentUser,
        Event $event,
        Asset $asset
    )
    {
        if(!$aboutCurrentUser->isAdmin())
            return $responseService->notAuthorized();

        return $responseService->successfullGetted($asset->toArray(), 'Asset');
    }
}
