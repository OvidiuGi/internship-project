<?php

namespace App\Repository;

use App\Entity\Room;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Room::class);
    }

    public function findAllRooms($maxParticipants): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT r
            FROM App\Entity\Room r
            WHERE r.capacity >= :maxParticipants'
        )->setParameter('maxParticipants', $maxParticipants);

        return $query->getResult();
    }
}
