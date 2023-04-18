<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\JWT\JWTService;
use App\Services\Response\ResponseService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AccountController extends Controller
{
    /**
     * Cree a new controller instance.
     * 
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth:api', ['except' =>['login']]);
    // }

    /**
     * register the user
     *
     * @param Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {

        // valider les dataInputs
        $validator = Validator::make($request->all(),
            [
                'name' => 'required|string|min:3|max:50',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:5|max:15|confirmed'
            ]
        );

        if($validator->fails())
            return $this->responseJson('error', 422, $validator->errors()); 

        try
        {
            // save the new user
            $user = new User();
            $user->name = $request['name'];
            $user->email = $request['email'];
            $user->password = sha1($request['password']);

            $user->email_verified_at = now();
            $user->remember_token = Str::random(10);
    
            $user->save();
            // dd($user->getAttributes());

            return $this->responseJson('success', 200, 'User successfully registered');
            
        } catch(Exception $e)
        {
            return response()->json($e);
        }
    }
    
    /**
     * Get a JWT via given credentials
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function login(JWTService $jWTService, ResponseService $responseService)
    {

        /**
         * Service validation
         * 
         */
        $credentials = request(['email', 'password']);
        // $credentials = $request->all();

        $validator = Validator::make($credentials,
            [
                'email' => 'required|email',
                'password' => 'required'
            ]
        );

        if($validator->fails())
            return $this->responseJson('error', 422, $validator->errors());


        /**
         * service verfication account in db
         * 
         */
        $user = User::where('email', request('email'))->first();

        if(!isset($user))
            return $this->responseJson('error', 422, 'User not identified');
        if($user->getAttribute('password') !== sha1(request('password')))
            return $this->responseJson('error', 422, 'Password incorrect');

    
        /**
         * service generation token
         * 
         */
        $token = $jWTService->generateToken($user);
        // send a new token refreshed in response
        $responseService->setRefreshToken($token);


        /**
         * response http
         * 
         */
        return $responseService->generateResponseJson(
            'success',
            200,
            'User successfully logged'
        );

    }

     /**
     * Log the user out (Invalidate the token).
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function logout(ResponseService $responseService)
    {
        return $responseService->generateResponseJson(
            'success',
            200,
            'User successfully logged'
        );
    }

}
