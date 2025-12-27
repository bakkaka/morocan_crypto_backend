<?php
namespace App\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\UserBankDetail;
use App\Entity\ChatMessage;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security; // CHANGÉ ICI

class CurrentUserExtension implements QueryCollectionExtensionInterface
{
    public function __construct(private Security $security) {} // CHANGÉ ICI

    public function applyToCollection(
        QueryBuilder $queryBuilder, 
        QueryNameGeneratorInterface $queryNameGenerator, 
        string $resourceClass, 
        ?Operation $operation = null, 
        array $context = []
    ): void {
        $user = $this->security->getUser();
        
        if (!$user) {
            return;
        }

        // Filtrer UserBankDetail par l'utilisateur connecté
        if (UserBankDetail::class === $resourceClass) {
            $rootAlias = $queryBuilder->getRootAliases()[0];
            $queryBuilder->andWhere("$rootAlias.user = :current_user");
            $queryBuilder->setParameter('current_user', $user->getId());
        }
        
        // Filtrer ChatMessage par l'utilisateur connecté
        if (ChatMessage::class === $resourceClass) {
            $rootAlias = $queryBuilder->getRootAliases()[0];
            
            // Version SIMPLIFIÉE d'abord - juste par sender
            $queryBuilder->andWhere("$rootAlias.sender = :current_user")
                        ->setParameter('current_user', $user->getId());
        }
    }
}