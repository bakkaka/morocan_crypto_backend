<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

class JWTResponseListener
{
    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $user = $event->getUser();
        
        // Ajoutez email au token si seul username est présent
        if (isset($data['username']) && !isset($data['email'])) {
            $data['email'] = $data['username'];
        }
        
        $event->setData($data);
    }
}
