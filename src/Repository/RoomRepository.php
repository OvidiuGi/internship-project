<?php

namespace App\Repository;

use App\Entity\Programme;
use App\Entity\Room;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnexpectedResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Room[]    findAll()
 */
class RoomRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        ManagerRegistry $registry,
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;

        parent::__construct($registry, Room::class);
    }

    /**
     * @throws UnexpectedResultException
     */
    public function findFirstAvailable(Programme $programme): Room
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('DISTINCT r')
            ->setMaxResults(1)
            ->from('App\Entity\Room', 'r')
            ->join('App\Entity\Programme', 'p')
            ->where('p.startTime >= :endTime')
            ->orWhere('p.endTime <= :startTime')
            ->groupBy('r.id')
            ->having('r.capacity >= :maxParticipants')
            ->setParameter('endTime', $programme->getEndTime())
            ->setParameter('startTime', $programme->getStartTime())
            ->setParameter('maxParticipants', $programme->maxParticipants)
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * @throws UnexpectedResultException
     */
    public function findFirstRoom(): Room
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('r')
            ->setMaxResults(1)
            ->from('App\Entity\Room', 'r')
            ->getQuery()
            ->getSingleResult();
    }
}
