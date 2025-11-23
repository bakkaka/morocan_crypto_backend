<?php
// src/Controller/AuthController.php

namespace App\Controller;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api')]
class AuthController extends AbstractController
{
    #[Route('/login_check', name: 'api_login', methods: ['POST'])]
    public function login(#[CurrentUser] ?User $user, JWTTokenManagerInterface $jwtManager): JsonResponse
    {
        // Cette méthode est appelée automatiquement après une authentification réussie
        if (!$user) {
            return new JsonResponse([
                'message' => 'Authentication failed'
            ], 401);
        }

        // Générer le token JWT
        $token = $jwtManager->create($user);

        return new JsonResponse([
            'token' => $token,
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'fullName' => $user->getFullName(),
                'phone' => $user->getPhone(),
                'roles' => $user->getRoles(),
                'isVerified' => $user->isVerified(),
                'reputation' => $user->getReputation(),
                'createdAt' => $user->getCreatedAt()->format('Y-m-d H:i:s'),
            ]
        ]);
    }

    #[Route('/me', name: 'api_me', methods: ['GET'])]
    public function me(#[CurrentUser] ?User $user): JsonResponse
    {
        if (!$user) {
            return new JsonResponse([
                'message' => 'Not authenticated'
            ], 401);
        }

        return new JsonResponse([
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'fullName' => $user->getFullName(),
                'phone' => $user->getPhone(),
                'roles' => $user->getRoles(),
                'isVerified' => $user->isVerified(),
                'reputation' => $user->getReputation(),
                'createdAt' => $user->getCreatedAt()->format('Y-m-d H:i:s'),
            ]
        ]);
    }

    #[Route('/logout', name: 'api_logout', methods: ['POST'])]
    public function logout(): JsonResponse
    {
        // Avec JWT, la déconnexion est côté client (supprimer le token)
        return new JsonResponse([
            'message' => 'Logout successful'
        ]);
    }
}