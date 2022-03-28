<?php

namespace App\Repository;

use App\Entity\Programme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class ProgrammeRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManager;

    public const PROGRAMME_FIELDS = [
        'id',
        'description',
        'startTime',
        'endTime',
        'trainer',
        'room',
        'isOnline',
        'maxParticipants',
    ];

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

    public function exactSearchByName($exactName): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('p')
            ->from('App\Entity\Programme', 'p')
            ->where('p.name LIKE :str')
            ->setParameter('exactName', $exactName)
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
            ->setParameter('str', '%'.$str.'%')
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

    public function getPaginatedFilteredSorted(
        array $paginate,
        array $filters,
        ?string $sort,
        ?string $direction
    ): array {
        $query = $this->entityManager
            ->createQueryBuilder()
            ->select('p')
            ->from('App\Entity\Programme', 'p')
            ->setFirstResult($paginate['size'] * ($paginate['page'] - 1))
            ->setMaxResults($paginate['size']);

        foreach ($filters as $key => $value) {
            if (null != $value) {
                $query->where("p.$key = :value");
                $query->setParameter(':value', $value);
            }
        }
        $direction = strtoupper($direction);

        if (!in_array($direction, ['ASC', 'DESC'])) {
            $direction = 'ASC';
        }

        if (null != $sort) {
            $query
                ->orderBy("p.$sort", $direction);
        }

        return $query->getQuery()->getResult();
    }
}
