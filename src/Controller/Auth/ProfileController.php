<?php

namespace App\Controller\Auth;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ProfileController extends AbstractController
{
    #[Route('/api/auth/me', name: 'api_auth_me', methods: ['GET'])]
    public function me(Security $security): JsonResponse
    {
        // Utilisez $this->getUser() qui fonctionne mieux
        /** @var User|null $user */
        $user = $this->getUser();
        
        if (!$user) {
            return new JsonResponse([
                'error' => 'Not authenticated',
                'message' => 'User not found'
            ], 401);
        }
        
        // ⭐ RETOURNEZ UN TABLEAU SIMPLE
        return new JsonResponse([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'fullName' => $user->getFullName() ?? '',
            'phone' => $user->getPhone() ?? '',
            'roles' => $user->getRoles(),
            'isVerified' => $user->isVerified(),
            'reputation' => $user->getReputation(),
            'isActive' => $user->isActive(),
            'walletAddress' => $user->getWalletAddress() ?? '',
            'createdAt' => $user->getCreatedAt() ? $user->getCreatedAt()->format('c') : null,
            'updatedAt' => $user->getUpdatedAt() ? $user->getUpdatedAt()->format('c') : null,
        ]);
    }
}