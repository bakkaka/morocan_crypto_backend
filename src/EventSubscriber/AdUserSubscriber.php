<?php

namespace App\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Ad;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;

class AdUserSubscriber implements EventSubscriberInterface
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['setUserOnAd', EventPriorities::PRE_VALIDATE],
        ];
    }

    public function setUserOnAd(ViewEvent $event): void
    {
        $ad = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        // Vérifier que c'est une Ad et une requête POST (création)
        if (!$ad instanceof Ad || Request::METHOD_POST !== $method) {
            return;
        }

        // Obtenir l'utilisateur connecté
        $user = $this->security->getUser();
        
        if (!$user) {
            throw new \RuntimeException('User must be authenticated to create an ad.');
        }

        // Définir l'utilisateur sur l'annonce
        $ad->setUser($user);
        
        // Debug log
        error_log('AdUserSubscriber: User ' . $user->getId() . ' set on ad');
    }
}