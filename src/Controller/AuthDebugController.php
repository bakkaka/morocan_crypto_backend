<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api/debug')]
class AuthDebugController extends AbstractController
{
    #[Route('/auth', name: 'api_debug_auth', methods: ['GET'])]
    public function authDebug(): JsonResponse
    {
        return $this->json([
            'security' => [
                'user' => $this->getUser() ? [
                    'class' => get_class($this->getUser()),
                    'id' => method_exists($this->getUser(), 'getId') ? $this->getUser()->getId() : null,
                    'email' => method_exists($this->getUser(), 'getEmail') ? $this->getUser()->getEmail() : null,
                ] : null,
                'is_authenticated' => $this->isGranted('IS_AUTHENTICATED_FULLY'),
            ],
            'message' => 'Debug auth endpoint'
        ]);
    }
    
    #[Route('/jwt', name: 'api_debug_jwt', methods: ['GET'])]
    public function jwtDebug(Request $request): JsonResponse
    {
        $authHeader = $request->headers->get('Authorization');
        
        return $this->json([
            'headers' => [
                'authorization_received' => $authHeader !== null,
                'authorization_preview' => $authHeader ? substr($authHeader, 0, 50) . '...' : null,
            ],
            'security' => [
                'user_exists' => $this->getUser() !== null,
                'user_class' => $this->getUser() ? get_class($this->getUser()) : null,
            ]
        ]);
    }
}