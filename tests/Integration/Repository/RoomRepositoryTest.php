<?php

namespace App\Tests\Integration\Repository;

use App\Repository\ProgrammeRepository;
use App\Repository\RoomRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RoomRepositoryTest extends KernelTestCase
{
    protected function runTest(): void
    {
        $this->markTestSkipped('Skipped test');
    }

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
        $testRoom = $roomRepository->findFirstAvailable($programme);
        $this->assertIsObject($testRoom);
    }
}
