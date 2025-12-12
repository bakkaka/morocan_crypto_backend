<?php

namespace App\EventSubscriber;

use App\Entity\UserBankDetail;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class UserBankDetailSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly Security $security
    ) {}

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof UserBankDetail) {
            return;
        }

        $user = $this->security->getUser();

        if (!$user) {
            throw new AccessDeniedHttpException('Vous devez être connecté pour créer des coordonnées bancaires.');
        }

        // Force l'association de l'utilisateur connecté
        $entity->setUser($user);
        
        // Debug logging (optionnel)
        // error_log('UserBankDetail créé par utilisateur ID: ' . $user->getId());
    }
}