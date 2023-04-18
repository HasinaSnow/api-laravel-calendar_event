<?php

namespace App\Http\Middleware;

use App\Services\Permission\PermissionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserPermission
{

    /**
     * instance of permissionService
     *
     * @var instance
     */
    private $permissionService;

    /**
     * get the instance of Permission servie
     *
     * @param PermissionService $permissionService
     */
    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // dd($this->permissionService->havePermission($request));

        if($this->permissionService->havePermission($request))
            return $next($request);

        return response()->json([
            'status' => 'error',
            'message' => 'User Unauthorized'
        ], 401);

    }
}
