<?php

namespace App\Services\Response;

use Illuminate\Http\JsonResponse;

class ResponseService
{
    /**
     * the token to send in response
     *
     * @var string
     */
    private $token = null;

    /**
     * generate a json response http with token jwt
     *
     * @param string $status
     * @param integer $status_code
     * @param string $message
     * @param string $data
     * @return void
     */
    public function generateResponseJson(string $status, int $status_code, string $message, array $data = [])
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'token' => $this->token,
            'data' => $data
        ], $status_code);

    }

    /**
     * send a not successfull getted response 
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function successfullGetted(array $data, $subject = 'Data'): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $subject . ' successffully getted',
            'token' => $this->token,
            'data' => $data
        ], 200);
    }

    /**
     * send a not successfull deleted response 
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function successfullDeleted($subject = 'Data'): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $subject . ' successfully deleted',
            'token' => $this->token,
            'data' => []
        ], 200);
    }

    /**
     * send a not successfull stored response 
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function successfullStored($subject = 'Data'): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $subject . ' successfully stored',
            'token' => $this->token,
            'data' => []
        ], 200);
    }

    /**
     * send a not successfull stored response 
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function successfullUpdated($subject = 'Data'): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $subject . ' successffully updated',
            'token' => $this->token,
            'data' => []
        ], 200);
    }

    /**
     * send a already existing response error 
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function alreadyExist($subject = 'Data'): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $subject . ' already exists in database',
            'token' => $this->token,
            'data' => []
        ], 500);
    }

    /**
     * send a not authorized response 
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function notAuthorized(): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => 'Not Authorized',
            'token' => $this->token,
            'data' => []
        ], 403);
    }

    /**
     * send a not found response 
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function notFound(): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => 'Not found',
            'token' => $this->token,
            'data' => []
        ], 404);
    }

    /**
     * send a server response error 
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function errorServer(): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => 'Error Server',
            'token' => $this->token,
            'data' => []
        ], 500);
    }

    /**
     * set the new token refreshed
     *
     * @param string $token
     * @return void
     */
    public function setRefreshToken(string $token)
    {
        $this->token = $token;
    }
}