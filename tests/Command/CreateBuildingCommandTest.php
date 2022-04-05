<?php

namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class CreateBuildingCommandTest extends KernelTestCase
{
    private CommandTester $commandTester;

    public function setUp(): void
    {
        parent::setUp();

        $kernel = self::bootKernel();

        $application = new Application($kernel);

        $command = $application->find('app:create-building');

        $this->commandTester = new CommandTester($command);
    }

    public function testExecuteWithNoArguments(): void
    {
        $this->commandTester->execute([]);

        $this->assertStringContainsString(
            'Not enough arguments (missing: "startTime, endTime").',
            $this->commandTester->getErrorOutput()
        );
    }
}
