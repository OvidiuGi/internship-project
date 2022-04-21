<?php

namespace App\Tests\Integration\Entity;

use App\Repository\BuildingRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BuildingTest extends KernelTestCase
{
    protected function runTest(): void
    {
        $this->markTestSkipped('Skipped test');
    }

    public function testGetId(): void
    {
        self::bootKernel();

        $container = static::getContainer();

        $buildingRepository = $container->get(BuildingRepository::class);
        $testBuilding = $buildingRepository->findAll()[0];
        $this->assertIsInt($testBuilding->getId());
    }
}
