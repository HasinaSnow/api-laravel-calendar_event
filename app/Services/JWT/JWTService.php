<?php

namespace App\Services\JWT;

use App\Models\User;
use Illuminate\Http\Request;

class JWTService
{

    /**
     * secrete key for decrypting a jwt
     *
     * @var string
     */
    private $secret;

    /**
     * the algorythme used for encodage or decodage
     *
     * @var string
     */
    private $alg;

    /**
     * the header's name of the token
     *
     * @var string
     */
    private $header;

    /**
     * the token is bearer
     * 
     * @var bool
     */
    private $bearer;

    /**
     * the time exipration of the token
     *
     * @var string
     */
    private $exp;

    /**
     * the current token genereated by the singleton instance
     *
     * @var string
     */
    private $currentToken;

    /**
     * 
     */
    public function __construct(string $secret, string $alg, string $header, bool $bearer = true, string $exp)
    {
        $this->secret = $secret;
        $this->alg = $alg;
        $this->header = $header;
        $this->bearer = $bearer;
        $this->exp = $exp;
    }

    /**
     * get the current token
     *
     * @return string|null
     */
    public function getCurrentToken() : string
    {
        return $this->currentToken;;
    }

    /**
     * save the current token into the request
     * 
     * @param Illuminate\Http\Request $request
     */
    public function setCurrentToken($request)
    {
        $this->currentToken = $this->getTokenInHeader($request);
    }

    /**
     * Genereate a jwt token contains the nessecary data of user
     * @param object $user the current user connected
     * @return string jwt token
     */
    public function generateToken($user)
    {
        $subject = [
            'id' => $user->getAttribute('id'),
            'name' => $user->getAttribute('name'),
            'email' => $user->getAttribute('email')
        ];
        // generate the token with data user
        return JWT::encode($subject, $this->alg, $this->secret, $this->exp);
    }
    
    /**
     * verifie and validate the jwt token with the signature of the secret key
     * @param Illuminate\Http\Request $request
     * @return bool
     */
    public function validateToken($request): bool
    {
        $token = $this->getTokenInHeader($request);

        if (!$token)
            return false;
        // verif the form
        if (!JWT::isValid($token))
            return false;
        // verif the signature
        if (!JWT::decode($token, $this->secret))
            return false;
        // verif the time expiration
        if (JWT::isExpired($token))
            return false;

        return true;
    }

    /**
     * Refresh the jwt token (and update the user and the time expiration)
     * @return string $token
     */
    public function getRefreshToken()
    {
        $user =  User::where('id', $this->getIdUserToken())->first();
        return $this->generateToken($user);
    }

    /**
     * Get the data user into the payload
     * @param string $token 
     * @return array $dataUser 
     */
    public function getDataUserToken(): array
    {
        // recuperer le payload
        $payload = JWT::getPayload($this->currentToken);
        // renvoyer l'id profile 
        return $payload['data'];
    }

    /**
     * get the user id into the current token
     *
     * @param string $token
     * @return integer
     */
    public function getIdUserToken(): int
    {
        return $this->getDataUserToken()['id'];
    }

    /**
     * verifie and get the token in Header of tne request
     *
     * @param Illuminate\Http\Request $request
     * @return null|string $token
     */
    private function getTokenInHeader($request)
    {
        // verifie the token in header
        $headers = getallheaders();
        // dd(isset($headers[$this->header]));
        if (!isset($headers[$this->header]))
            return false;


        if ($this->bearer) 
            return explode(' ', $headers[$this->header])[1];
        else
            return $headers[$this->header];
    }

    /**
     * get the payload
     *
     * @return array
     */
    public function getPayloadData(): array
    {
        return [];
    }
}
