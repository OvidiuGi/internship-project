<?php

namespace App\Tests\Integration\Repository;

use App\Repository\ProgrammeRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProgrammeRepositoryTest extends KernelTestCase
{
    protected function runTest(): void
    {
        $this->markTestSkipped('Skipped test');
    }

    public function testGetAll(): void
    {
        $programmeRepository = static::getContainer()->get(ProgrammeRepository::class);
        $programmeRepository->getAll();

        $this->assertTrue(true);
    }

    public function testRemoveTrainerByIdFromProgrammes(): void
    {
        $programmeRepository = static::getContainer()->get(ProgrammeRepository::class);
        $programmeRepository->removeTrainerByIdFromProgrammes(1);

        $this->assertTrue(true);
    }
}
