<?php
namespace App\Services\JWT;

use DateTime;

class JWT
{

    /**
     * generer le un token jwt
     * @param array $data la donnée à injecter dans le jeton
     * @param array $alg l'algorithme utililiser pour le criptage/decriptage du jeton
     * @param  string $secret la clé privée caché à utiliser pour generer la signature du token
     * @param  int $exp la periode (en seconde) de validité du token
     * @return string $token le jeton
     */
    public static function encode(array $data, string $alg, string $secret , int $exp): string
    {

        // on cree le header
        $header = array(
            'typ' => 'JWT',
            'alg' => $alg
        );
        
        // creer le payload
        $now = new DateTime();
        $payload = array(
            'iat' => $now->getTimestamp(),
            'exp' => $now->getTimestamp() + $exp,
            'data' => $data
        );

        // on cree l'encodage en base64
        $base64Header = base64_encode(json_encode($header));
        $base64Payload = base64_encode(json_encode($payload));

        // on filtre les valeurs encodées (remplacer les +,=,/ qui ne sont pas supportées)
        $base64Header = str_replace(['+', '=', '/'], ['-', '', '_'], $base64Header);
        $base64Payload = str_replace(['+', '=', '/'], ['-', '', '_'], $base64Payload);

        // on génère la signature
        $secret = base64_encode($secret); // encoder mon secret
        $signature = hash_hmac('sha256', $base64Header . '.' . $base64Payload, $secret, true); // concactener les trois (header, payload, secret)
       
        // on filtre les valeurs encodées (remplacer les +,=,/ qui ne sont pas supportées)
        $signature = str_replace(['+', '=', '/'], ['-', '', '_'], base64_encode($signature));

        // on cree le token
        $jwt = $base64Header . '.' . $base64Payload . '.' . $signature;

        // on retourne le token
        return $jwt;
    }

    /**
     * verifer la validation de signature du token
     * @param string $token le token à verifier
     * @param string $secret le secret de la signature
     * @return bool
     */
    public static function decode(string $token, string $secret) : bool
    {
        
        // on recupère le header et le payload
        $parts = explode('.', $token);
        $header = $parts[0];
        $payload = $parts[1];
        $signature = $parts[2];
        
        
        // on cree l'encodage du secret en base64
        $secret = base64_encode($secret); // encoder mon secret
        
        // verifier et filtrer la signature 
        $isTrueSignature = hash_hmac('sha256', $header . '.' . $payload, $secret, true);
        $isTrueSignature = str_replace(['+', '=', '/'], ['-', '', '_'], base64_encode($isTrueSignature));
        
        return $isTrueSignature === $signature;
    }

    /**
     * verifier si le token est expiré (date d'expiration)
     * @param string $token
     * @return bool
     */
    public static function isExpired(string $token){
        // recuperer le payload
        $payload = self::getPayload($token);

        // verifier que la date d'expiration du token est inferieur à la date maintenant
        $now = new DateTime();
        return $payload['exp'] < $now->getTimestamp();
    }

    /**
     * verifier si l'expression du token est valide (non pas en tant que signature)
     * @param string $token
     * @return bool
     */
    public static function isValid(string $token) : bool
    {
        return preg_match(
            '/^[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+$/',
            $token
        ) === 1;
    }

    /**
     * recuperer le header du token
     * @param $token 
     * @return array $header
     */
    public static function getHeader(string $token) : array
    {
        // on démonte le token
        $array = explode('.', $token);
        $header = json_decode(base64_decode($array[0]), true);

        return $header;
    }

    /**
     * recupererl le payload (contenu) du token
     * @param string $token 
     * @return array $payload
     */
    public static function getPayload(string $token) : array
    {
        // on démonte le token
        $array = explode('.', $token);
        $payload = json_decode(base64_decode($array[1]), true);

        return $payload;
    }

}
