<?php

namespace App\Http\Controllers;

use App\Helpers\AboutUser;
use App\Models\Asset;
use App\Models\Event;
use App\Services\Response\ResponseService;

class EventAssetController extends Controller
{
    public function indexAssets(
        ResponseService $responseService,
        AboutUser $aboutUser,
        Event $event
    )
    {
        if(!$aboutUser->isAdmin())
            return $responseService->notAuthorized();

        $assets = Asset::where('event_id', $event->id)
        ->with(['event', 'money'])
        ->get();

        return $responseService->successfullGetted($assets->toArray(), 'Assets');
    }

    public function showAsset(
        ResponseService $responseService,
        AboutUser $aboutUser,
        Event $event,
        Asset $asset
    )
    {
        if(!$aboutUser->isAdmin())
            return $responseService->notAuthorized();

        return $responseService->successfullGetted($asset->toArray(), 'Asset');
    }
}
