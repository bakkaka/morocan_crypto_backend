<?php

namespace App\Repository;

use App\Entity\UserBankDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserBankDetailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserBankDetail::class);
    }
    
    // Méthodes supplémentaires OPTIONNELLES
    public function findActiveByUser(int $userId): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.user = :userId')
            ->andWhere('u.isActive = true')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }



// Trouver par banque
public function findByBankName(string $bankName): array
{
    return $this->createQueryBuilder('ubd')
        ->andWhere('ubd.bankName = :bankName')
        ->andWhere('ubd.isActive = true')
        ->setParameter('bankName', $bankName)
        ->getQuery()
        ->getResult();
}

// Désactiver toutes les anciennes cartes bancaires
public function deactivateOtherDetails(int $userId, int $currentDetailId): void
{
    $this->createQueryBuilder('ubd')
        ->update()
        ->set('ubd.isActive', 'false')
        ->where('ubd.user = :userId')
        ->andWhere('ubd.id != :currentId')
        ->setParameter('userId', $userId)
        ->setParameter('currentId', $currentDetailId)
        ->getQuery()
        ->execute();
}
}