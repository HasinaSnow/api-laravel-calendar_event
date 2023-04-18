<?php

namespace App\Http\Controllers;

use App\Models\EventTask;
use App\Services\JWT\JWTService;
use App\Services\Response\ResponseService;
use Illuminate\Http\Request;

class EventTaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(
        JWTService $jWTService,
        ResponseService $responseService
    )
    {
        // recuperer les tasks appartenant aux events dont l'user courant p
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
    public function show(EventTask $eventTask)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EventTask $eventTask)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EventTask $eventTask)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EventTask $eventTask)
    {
        //
    }
}
