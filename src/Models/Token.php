<?php

namespace Source\Models;

use DateTime;
use DateTimeZone;
use Exception;
use Firebase\JWT\JWT;

/**
 * Abstract Class Token
 * @package Source\Models
 */
abstract class Token
{
    /**
     * Tempo de expiração do token em segundos
     * 
     * @var int $expireTokenTime
     */
    private static int $expireTokenTime = 86400;

    /**
     * Generate token of user
     *
     * @param User $user
     * @return string
     */
    public static function token(User $user): string
    {
        $time = new DateTime('now', new DateTimeZone("America/Bahia"));
        $timestamp = $time->getTimestamp() + $time->getOffset();

        $tokenContent = [
            "iat" => $timestamp,
            "exp" => $timestamp + self::$expireTokenTime,
            "data" => [
                "id" => $user->id,
                "userName" => $user->user_name,
            ]
        ];

        return JWT::encode($tokenContent, $_ENV['ENV_JWT_HASH']);
    }

    /**
     * Decodificação de determinado token
     *
     * @param string $token
     * @return object|null
     */
    public static function decodeToken(string $token): ?object
    {
        try {
            return JWT::decode($token, $_ENV["ENV_JWT_HASH"], ['HS256']);
        } catch (Exception $e) {
            return null;
        }
    }
}