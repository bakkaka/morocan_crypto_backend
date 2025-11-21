<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class CorsSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => ['onKernelResponse', 0],
        ];
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $request = $event->getRequest();
        $response = $event->getResponse();

        // Autorisez toutes les origines ou spécifiez les vôtres
        $allowedOrigins = [
            'http://localhost:5175',
            'http://127.0.0.1:5175', 
            'http://localhost:3000',
            'http://127.0.0.1:3000'
        ];

        $origin = $request->headers->get('Origin');
        
        if (in_array($origin, $allowedOrigins)) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
        }

        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, Accept');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
        $response->headers->set('Access-Control-Max-Age', '3600');

        // Gestion des requêtes OPTIONS (preflight)
        if ($request->getMethod() === 'OPTIONS') {
            $response->setStatusCode(Response::HTTP_OK);
        }
    }
}