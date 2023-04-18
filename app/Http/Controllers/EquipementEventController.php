<?php

namespace App\Http\Controllers;

use App\Models\EquipementEvent;
use App\Models\Event;
use App\Services\Response\ResponseService;
use Illuminate\Http\Request;

class EquipementEventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(
        ResponseService $responseService
    )
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(EquipementEvent $equipementEvent)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EquipementEvent $equipementEvent)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EquipementEvent $equipementEvent)
    {
        //
    }
}
