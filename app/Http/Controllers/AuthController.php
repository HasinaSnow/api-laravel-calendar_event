<?php

namespace App\Http\Controllers;

class AuthController extends Controller
{
    /**
     * Cree a new controller instance.
     * 
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' =>['login']]);
    }

    /**
     * get the token array structure
     *
     * @param string $token
     * @return Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer'
            // 'expires_in' => auth()->factory()->gett *60
        ]);
    }


    /**
     * Get a JWT via given credentials
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if(!$token = auth()->attempt($credentials))
        {
            return response()->json(['error' =>'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * refresh the token
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->resondWithToken(auth()->refresh);
    }

}
