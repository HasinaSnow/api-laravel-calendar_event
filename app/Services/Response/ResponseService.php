<?php

namespace App\Services\Response;


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