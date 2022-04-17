<?php

namespace App\Tests\Functional\Repository;

use App\Entity\Room;
use App\Repository\ProgrammeRepository;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RoomRepositoryTest extends KernelTestCase
{
    public function testFindFirstRoom(): void
    {
        $roomRepository = static::getContainer()->get(RoomRepository::class);
        $testRoom = $roomRepository->findFirstRoom();
        $this->assertIsObject($testRoom);
    }

    public function testFindFirstAvailable(): void
    {
        $roomRepository = static::getContainer()->get(RoomRepository::class);
        $programmeRepository = static::getContainer()->get(ProgrammeRepository::class);
        $programme = $programmeRepository->findAll()[0];
        $testRoom = $roomRepository->findFirstAvailable(
            $programme->getStartTime(),
            $programme->getEndTime(),
            $programme->maxParticipants,
            $programme->isOnline
        );
        $this->assertIsObject($testRoom);
    }
}
