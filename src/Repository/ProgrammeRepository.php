<?php

namespace App\Repository;

use App\Entity\Programme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProgrammeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Programme::class);
    }

    public function getOccupiedRoomId($startTime, $endTime): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT DISTINCT r.id
            FROM App\Entity\Programme p
            LEFT JOIN p.room r
            WHERE p.startTime < :endTime
             AND p.endTime > :startTime'
        )->setParameter('endTime', $endTime);
        $query->setParameter('startTime', $startTime);

        return $query->getResult();
    }
}
