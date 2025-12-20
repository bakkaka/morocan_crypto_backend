<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class DebugController extends AbstractController
{
    #[Route('/api/debug-register', name: 'api_debug_register', methods: ['POST'])]
    public function debugRegister(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            error_log("🎯 DEBUG: Données reçues: " . print_r($data, true));
            
            // Créer un nouvel utilisateur
            $user = new User();
            $user->setEmail($data['email']);
            $user->setFullName($data['fullName']);
            $user->setPhone($data['phone']);
            
            error_log("🎯 DEBUG: User créé - Email: " . $user->getEmail());
            
            // Hasher le mot de passe MANUELLEMENT
            $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
            $user->setPassword($hashedPassword);
            
            error_log("🎯 DEBUG: Password hashé avec succès");
            
            // Persister et sauvegarder
            $em->persist($user);
            $em->flush();
            
            error_log("🎯 DEBUG: User sauvegardé en base - ID: " . $user->getId());
            
            return $this->json([
                'success' => true,
                'message' => 'Utilisateur créé avec succès via debug',
                'user_id' => $user->getId(),
                'user_email' => $user->getEmail()
            ], 201);
            
        } catch (\Exception $e) {
            error_log("💥 ERREUR DEBUG: " . $e->getMessage());
            error_log("💥 STACK TRACE: " . $e->getTraceAsString());
            
            return $this->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}