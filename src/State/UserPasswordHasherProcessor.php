<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserPasswordHasherProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly ProcessorInterface $processor,
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {}

    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if (!$data instanceof User) {
            return $this->processor->process($data, $operation, $uriVariables, $context);
        }

        $plainPassword = $data->getPlainPassword();
        
        if ($plainPassword) {
            $hashedPassword = $this->passwordHasher->hashPassword($data, $plainPassword);
            $data->setPassword($hashedPassword);
            // Effacez le plainPassword après hachage
            $data->setPlainPassword(null);
        }

        return $this->processor->process($data, $operation, $uriVariables, $context);
    }
}