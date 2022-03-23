<?php

namespace App\Repository;

use App\Entity\Programme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
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
        return $this->entityManager
            ->createQueryBuilder()
            ->select('p')
            ->from('App\Entity\Programme', 'p')
            ->getQuery()
            ->execute();
    }

    public function exactSearchByName($str): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('DISTINCT p')
            ->from('App\Entity\Programme', 'p')
            ->where('p.name LIKE :str')
            ->setParameter('str', $str)
            ->getQuery()
            ->execute();
    }

    public function partialSearchByName($str): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('DISTINCT p')
            ->from('App\Entity\Programme', 'p')
            ->where('p.name LIKE :str')
            ->setParameter('str', '%' . $str . '%')
            ->getQuery()
            ->execute();
    }

    public function getSorted(string $field, string $sortType): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('p')
            ->from('App\Entity\Programme', 'p')
            ->orderBy("p.$field", $sortType)
            ->getQuery()
            ->execute();
    }

    public function getPaginated(int $page, int $limit): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('p')
            ->from('App\Entity\Programme', 'p')
            ->setFirstResult(($page * $limit) - $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->execute();
    }
}
