<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class AuthController extends AbstractController
{
    /**
     * Le traitement réel est fait par json_login + LexikJWT
     * Cette méthode ne sert que pour documenter l’endpoint
     */
    #[Route('/login_check', name: 'api_login_check', methods: ['POST'])]
    public function loginCheck(): void
    {
        // Cette méthode ne sera jamais appelée
        // LexikJWT intercepte avant
        throw new \LogicException('This code should never be reached.');
    }

    #[Route('/logout', name: 'api_logout', methods: ['POST'])]
    public function logout(): JsonResponse
    {
        // JWT = stateless → logout côté client
        return $this->json([
            'message' => 'Logout successful'
        ]);
    }
}
