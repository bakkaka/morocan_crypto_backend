<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JWTCreatedListener
{
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $user = $event->getUser();
        $payload = $event->getData();
        
        // 1. Ajouter l'ID (CRITIQUE pour /api/users/me)
        $payload['id'] = $user->getId();
        
        // 2. Ajouter email explicitement
        $payload['email'] = $user->getEmail();
        
        // 3. S'assurer que username est bien l'email
        $payload['username'] = $user->getEmail();
        
        $event->setData($payload);
    }
}