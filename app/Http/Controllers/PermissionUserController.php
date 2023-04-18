<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\JWT\JWTService;
use Illuminate\Http\Request;

class PermissionUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(JWTService $jWTService, Request $request)
    {
        return 'permision User index';
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return 'permision User store';
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return 'permision User show';
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        return 'permision User update';
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        return 'permision User destroy';
    }
}
