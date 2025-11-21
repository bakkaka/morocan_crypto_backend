<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    // ⭐ CHANGEZ LA ROUTE POUR /api/login
    #[Route('/api/auth/login', name: 'app_api_login', methods: ['POST'])]
    public function loginApi(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';
        
        if (!$email || !$password) {
            return $this->json([
                'message' => 'Email et mot de passe requis'
            ], 400);
        }

        // Trouver l'utilisateur par email
        $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);
        
        if (!$user) {
            return $this->json([
                'message' => 'Utilisateur non trouvé'
            ], 401);
        }

        // Vérifier le mot de passe
        if (!$passwordHasher->isPasswordValid($user, $password)) {
            return $this->json([
                'message' => 'Mot de passe incorrect'
            ], 401);
        }

        // Connexion réussie
        return $this->json([
            'message' => 'Connexion réussie',
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'fullName' => $user->getFullName(),
                'phone' => $user->getPhone(),
                'isVerified' => $user->isVerified(),
            ]
        ]);
    }

    // Gardez l'ancienne route pour le template HTML si nécessaire
    #[Route('/login', name: 'app_login_page', methods: ['GET'])]
    public function loginPage(AuthenticationUtils $authenticationUtils): JsonResponse
    {
        if ($this->getUser()) {
            return $this->json([
                'message' => 'Already logged in',
                'user' => $this->getUser()->getUserIdentifier()
            ], 200);
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->json([
            'message' => 'Login page endpoint',
            'last_username' => $lastUsername,
            'error' => $error ? $error->getMessage() : null
        ], 200);
    }

    #[Route('/api/logout', name: 'app_logout', methods: ['POST'])]
    public function logout(): JsonResponse
    {
        return $this->json([
            'message' => 'Déconnexion réussie'
        ]);
    }
}