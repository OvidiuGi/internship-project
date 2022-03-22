<?php

namespace App\Repository;

use App\Entity\Room;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

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

    public function getAvailableRoomId($startTime, $endTime, $maxParticipants): array
    {
        $query = $this->entityManager
            ->createQueryBuilder()
            ->select('DISTINCT r.id,r.capacity')
            ->from('App\Entity\Programme', 'p')
            ->join('App\Entity\Room', 'r')
            ->where('p.startTime >= :endTime')
            ->orWhere('p.endTime <= :startTime')
            ->groupBy('r.id')
            ->having('r.capacity >= :maxParticipants')
            ->setParameter('endTime', $endTime)
            ->setParameter('startTime', $startTime)
            ->setParameter('maxParticipants', $maxParticipants);

        return $query->getQuery()->execute();
    }

    public function findById($id): array
    {
        $query = $this->entityManager
            ->createQueryBuilder()
            ->select('r')
            ->from('App\Entity\Room', 'r')
            ->where('r.id = :id')
            ->setParameter('id', $id);

        return $query->getQuery()->execute();
    }

    public function assignRoom($startTime, $endTime, $maxParticipants, $isOnline): Room
    {
        $availableRooms = $this->getAvailableRoomId($startTime, $endTime, $maxParticipants);
        $assignedRoom = new Room();
        if (count($availableRooms) > 0) {
            $assignedRoom = $this->findById($availableRooms[0]['id']);
            $newRoom = new Room();
            foreach ($assignedRoom as $key => $value) {
                $newRoom->$key = $value;
            }

            return $newRoom;
        }

        return new Room();
    }
}
