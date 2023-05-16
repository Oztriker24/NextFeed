<?php

namespace App\Service;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;

class AccessService
{
    public function __construct(
        private readonly JWTEncoderInterface $jwtEncoderInterface,
        private readonly UserRepository $userRepository,
    ) {
    }

    public function isUser($jwtToken): bool
    {
        try {
            $user = $this->jwtEncoderInterface->decode($jwtToken)["roles"];
        } catch (JWTDecodeFailureException $e) {
            if ($e->getReason() === "invalid_token" || $e->getReason() === "expired_token") {
                return false;
            }
        }
        if (in_array($_ENV["USER"], $user, true)) {
            return true;
        }
        return false;
    }

    public function isAdmin($jwtToken)
    {
        try {
            $user = $this->jwtEncoderInterface->decode($jwtToken)["roles"];
        } catch (JWTDecodeFailureException $e) {

            if ($e->getReason() === "invalid_token" || $e->getReason() === "expired_token") {
                return false;
            }
        }
        if (in_array($_ENV["ADMIN"], $user, true)) {
            return true;
        }
        return false;
    }
    public function decodeToken($jwtToken) {
        try {
            $user = $this->jwtEncoderInterface->decode($jwtToken);
        } catch (JWTDecodeFailureException $e) {
            return false ;
        }

        return $user["username"];
    }
}
