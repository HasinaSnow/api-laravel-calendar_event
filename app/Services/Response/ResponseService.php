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
     * all the notifications to sending in response
     */
    private $notifications = [];

    /**
     * generate a json response http with token jwt
     *
     * @param string $status
     * @param integer $status_code
     * @param string $message
     * @param string $data
     * @return void
     */
    public function generateResponseJson(string $status, int $status_code, string $message, array $data = []): JsonResponse
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'token' => $this->token,
            'data' => $data,
            'notifications' => $this->notifications
        ], $status_code);

    }

    /**
     * send a not successfull getted response 
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function successfullGetted(array $data, $subject = 'Data'): JsonResponse
    {
        return $this->generateResponseJson(
            'success',
            200,
            $subject . ' successffully getted',
            $data
        );
    }

    /**
     * send a not successfull deleted response 
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function successfullDeleted($subject = 'Data'): JsonResponse
    {
        return $this->generateResponseJson(
            'success',
            200,
            $subject . ' successfully deleted',
            []
        );
    }

    /**
     * send a not successfull stored response 
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function successfullStored($subject = 'Data'): JsonResponse
    {
        return $this->generateResponseJson(
            'success',
            200,
            $subject . ' successfully stored',
            []
        );
    }

    /**
     * send a successfull attached response
     */
    public function successfullAttached(string $subject = 'Data', string $with = 'current post'): JsonResponse
    {
        return $this->generateResponseJson(
            'success',
            200,
            $subject . ' successfully attached with ' . $with,
            []
        );
    }

    /**
     * send a succefull detached response 
     */
    public function successfullDetached(string $subject = 'Data', string $with = 'current post'): JsonResponse
    {
        return $this->generateResponseJson(
            'success',
            200,
            $subject . ' successfully detached with ' . $with,
            []
        );
    }

    /**
     * send a not successfull stored response 
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function successfullUpdated($subject = 'Data'): JsonResponse
    {
        return $this->generateResponseJson(
            'success',
            200,
            $subject . ' successffully updated',
            []
        );
    }

    /**
     * send a already existing response error 
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function alreadyExist($subject = 'Data'): JsonResponse
    {
        return $this->generateResponseJson(
            'error',
            500,
            $subject . ' already exists in database',
            []
        );
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
     * send a not authorized response 
     * @param string $data
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function notExists(string $data = 'This Data'): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $data . ' is not exists in Database.',
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

    /**
     * set the new notifications refreshed
     *
     * @param array|null $notifications
     * @return void
     */
    public function setNotifications(array|null $notifications)
    {
        $this->notifications = $notifications;
    }
}