<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

class AuthenticationSuccessListener
{
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $event->setData([
            'authentication' => 'success',
            'clientId' => $event->getUser()->getId(),
            'payload' => $event->getData()
        ]);
    }
}
