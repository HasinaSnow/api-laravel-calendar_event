<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\user\LoginUserRequest;
use App\Http\Requests\user\RegisterUserRequest;
use App\Models\User;
use Carbon\Exceptions\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    
    
    public function login(LoginUserRequest $request)
    {
        // si l'utilisateur est dans la bd
        if(auth()->attempt($request->only(['email', 'password'])))
        {
           $user = auth()->user();

           $token = $user->createToken('KEY_TOKEN')->plainTexttoken;

           return response()->json([
            'status_code' => 200,
            'message' => 'user connected',
            'data' => $user
        ]);

        } else
        {
            return response()->json([
                'status_code' => 403,
                'message' => 'user not found',
                'data' => []
            ]);
        }
    }


}
