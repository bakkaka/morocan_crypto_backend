<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/test')]
class TestController extends AbstractController
{
    #[Route('/public', name: 'api_test_public', methods: ['GET'])]
    public function publicEndpoint(): JsonResponse
    {
        return $this->json([
            'message' => 'Public endpoint - accessible sans authentification',
            'user' => $this->getUser() ? 'present' : 'null'
        ]);
    }
    
    #[Route('/protected', name: 'api_test_protected', methods: ['GET'])]
    public function protectedEndpoint(): JsonResponse
    {
        $user = $this->getUser();
        
        if (!$user) {
            return $this->json(['error' => 'Protected endpoint - authentication required'], 401);
        }
        
        return $this->json([
            'message' => 'Protected endpoint - accessible avec authentification',
            'user' => [
                'id' => method_exists($user, 'getId') ? $user->getId() : null,
                'email' => method_exists($user, 'getEmail') ? $user->getEmail() : null,
                'class' => get_class($user)
            ]
        ]);
    }
}