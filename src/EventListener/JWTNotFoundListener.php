<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTNotFoundEvent;
use Symfony\Component\HttpFoundation\JsonResponse;

class JWTNotFoundListener
{
    public function onJWTNotFound(JWTNotFoundEvent $event)
    {
        $event->setResponse(new JsonResponse([
            'status'  => 401,
            'message' => 'Missing credentials.',
            ], 401)
        );
    }
}