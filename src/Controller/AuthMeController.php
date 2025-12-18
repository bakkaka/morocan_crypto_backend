<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/auth')]
class AuthMeController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}
    
    #[Route('/me', name: 'api_auth_me', methods: ['GET'])]
    public function me(): JsonResponse
    {
        $user = $this->getUser();
        
        if (!$user || !$user instanceof User) {
            return $this->json(['message' => 'Unauthorized', 'code' => 401], 401);
        }
        
        // Charger l'User SANS les relations ApiPlatform
        $simpleUser = $this->entityManager->getRepository(User::class)->find($user->getId());
        
        if (!$simpleUser) {
            return $this->json(['message' => 'User not found', 'code' => 404], 404);
        }

        return $this->json([
            'id' => $simpleUser->getId(),
            'email' => $simpleUser->getEmail(),
            'fullName' => $simpleUser->getFullName(),
            'phone' => $simpleUser->getPhone(),
            'roles' => $simpleUser->getRoles(),
            'isVerified' => $simpleUser->isVerified(),
            'reputation' => $simpleUser->getReputation(),
            'isActive' => $simpleUser->isActive(),
            'walletAddress' => $simpleUser->getWalletAddress(),
            'createdAt' => $simpleUser->getCreatedAt()?->format('c'),
            'updatedAt' => $simpleUser->getUpdatedAt()?->format('c'),
        ]);
    }
}