<?php

namespace App\Repository;

use App\Entity\Programme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class ProgrammeRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct($registry, Programme::class);
    }

    public function getAll(): array
    {
        $query = $this->entityManager
            ->createQueryBuilder()
            ->select('p')
            ->from('App\Entity\Programme', 'p');

        return $query->getQuery()->execute();
    }
}
