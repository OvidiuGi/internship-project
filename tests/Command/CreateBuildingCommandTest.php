<?php

namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class CreateBuildingCommandTest extends KernelTestCase
{
    private CommandTester $commandTester;

    protected function runTest(): void
    {
        $this->markTestSkipped('Skipped test');
    }

    public function setUp(): void
    {
        parent::setUp();

        $kernel = self::bootKernel();

        $application = new Application($kernel);

        $command = $application->find('app:create-building');

        $this->commandTester = new CommandTester($command);
    }

    public function testExecuteSuccessfully(): void
    {
        $this->commandTester->execute(['startTime' => '08:00',
            'endTime' => '20:00']);

        $this->assertStringContainsString(
            'Building was successfully created!',
            $this->commandTester->getDisplay()
        );
    }
}
