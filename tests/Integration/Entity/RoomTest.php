<?php

namespace App\Tests\Integration\Entity;

use App\Repository\RoomRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RoomTest extends KernelTestCase
{
    protected function runTest(): void
    {
        $this->markTestSkipped('Skipped test');
    }

    public function testGetId(): void
    {
        self::bootKernel();

        $container = static::getContainer();

        $buildingRepository = $container->get(RoomRepository::class);
        $testBuilding = $buildingRepository->findAll()[0];
        $this->assertIsInt($testBuilding->getId());
    }
}
