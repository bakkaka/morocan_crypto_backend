<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class AuthController extends AbstractController
{
    #[Route('/login', name: 'app_login', methods: ['POST'])]
    public function login(#[CurrentUser] ?User $user, Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        if (!$user) {
            // Si l'authentification a échoué
            $data = json_decode($request->getContent(), true);
            $email = $data['email'] ?? '';
            $password = $data['password'] ?? '';
            
            // Vérification manuelle
            $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);
            
            if (!$user || !$passwordHasher->isPasswordValid($user, $password)) {
                return $this->json([
                    'message' => 'Email ou mot de passe incorrect',
                    'error' => 'Invalid credentials'
                ], 401);
            }
            
            // Si les identifiants sont valides mais que Symfony n'a pas authentifié
            return $this->json([
                'message' => 'Login réussi',
                'user' => [
                    'id' => $user->getId(),
                    'email' => $user->getEmail(),
                    'fullName' => $user->getFullName(),
                    'roles' => $user->getRoles(),
                ],
                // ⭐ Vous pouvez ajouter un token JWT ici plus tard
            ]);
        }

        // Si l'utilisateur est déjà authentifié par Symfony
        return $this->json([
            'message' => 'Login réussi',
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'fullName' => $user->getFullName(),
                'roles' => $user->getRoles(),
            ],
        ]);
    }

    #[Route('/logout', name: 'app_logout', methods: ['POST'])]
    public function logout(): JsonResponse
    {
        return $this->json([
            'message' => 'Déconnexion réussie'
        ]);
    }
}