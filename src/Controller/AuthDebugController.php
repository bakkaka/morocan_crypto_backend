<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/api/debug')]
class AuthDebugController extends AbstractController
{
    private $entityManager;
    private $jwtManager;
    private $tokenStorage;

    public function __construct(
        EntityManagerInterface $entityManager,
        JWTTokenManagerInterface $jwtManager,
        TokenStorageInterface $tokenStorage
    ) {
        $this->entityManager = $entityManager;
        $this->jwtManager = $jwtManager;
        $this->tokenStorage = $tokenStorage;
    }

    #[Route('/auth-test', name: 'api_debug_auth', methods: ['GET'])]
    public function authTest(): JsonResponse
    {
        try {
            $token = $this->tokenStorage->getToken();
            $user = $this->getUser();
            
            $data = [
                'authentication' => [
                    'has_token' => $token !== null,
                    'token_class' => $token ? get_class($token) : null,
                    'token_authenticated' => $token ? $token->isAuthenticated() : false,
                    'token_user_class' => $token && $token->getUser() ? get_class($token->getUser()) : null,
                ],
                'user' => [
                    'has_user' => $user !== null,
                    'user_class' => $user ? get_class($user) : null,
                    'user_id' => $user && method_exists($user, 'getId') ? $user->getId() : null,
                    'user_email' => $user && method_exists($user, 'getEmail') ? $user->getEmail() : null,
                    'is_user_instance' => $user instanceof UserInterface,
                ],
                'doctrine' => [
                    'user_managed' => $user ? $this->entityManager->contains($user) : false,
                ]
            ];

            return $this->json($data);

        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Exception in debug',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    #[Route('/jwt-info', name: 'api_debug_jwt', methods: ['GET'])]
    public function jwtInfo(Request $request): JsonResponse
    {
        $authHeader = $request->headers->get('Authorization');
        
        $token = null;
        if ($authHeader && preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            $token = $matches[1];
        }

        $decoded = null;
        if ($token) {
            try {
                $decoded = $this->jwtManager->parse($token);
            } catch (\Exception $e) {
                $decoded = ['error' => $e->getMessage()];
            }
        }

        return $this->json([
            'headers' => [
                'has_auth_header' => !empty($authHeader),
                'auth_header_preview' => $authHeader ? substr($authHeader, 0, 50) . '...' : null,
            ],
            'token' => [
                'extracted' => !empty($token),
                'length' => $token ? strlen($token) : 0,
                'parts' => $token ? count(explode('.', $token)) : 0,
                'decoded' => $decoded,
            ],
            'security' => [
                'firewall' => $request->attributes->get('_firewall_context'),
                'route' => $request->attributes->get('_route'),
            ]
        ]);
    }
}