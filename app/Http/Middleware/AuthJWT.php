<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\JWT\JWTService;
use App\Services\Notification\NotificationService;
use App\Services\Response\ResponseService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthJWT
{
    /**
     * the instance of jwtService
     *
     * @var instance
     */
    private $jWTService;

    /**
     * the instance of responseService
     *
     * @var instance
     */
    private $responseService;

    /**
     * get the instance of JWTService
     *
     * @param JWTService $jWTService
     */
    public function __construct(JWTService $jWTService, ResponseService $responseService)
    {
        $this->jWTService = $jWTService;
        $this->responseService = $responseService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        // verifie the token
        if ($this->jWTService->validateToken($request))
        {
            // save the token
            $this->jWTService->setCurrentToken($request);

            // send a new token refreshed in response
            $this->responseService->setRefreshToken($this->jWTService->getRefreshToken());

            // send a new unread notifications in response
            $user = User::find($this->jWTService->getIdUserToken());
            $notifications = new NotificationService();
            $this->responseService->setNotifications($notifications->get($user));

            return $next($request);

        } else{

            return response()->json([
                'status'=> 'error',
                'message' => 'User not Authentified'
            ],403);

        }
    }

}
