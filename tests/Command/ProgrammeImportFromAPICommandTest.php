<?php

namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Exception\MissingInputException;
use Symfony\Component\Console\Tester\CommandTester;

class ProgrammeImportFromAPICommandTest extends KernelTestCase
{
    private CommandTester $commandTester;

    public function setUp(): void
    {
        parent::setUp();

        $kernel = self::bootKernel();

        $application = new Application($kernel);

        $command = $application->find('app:create-account');

        $this->commandTester = new CommandTester($command);
    }

    public function testInvalidData(): void
    {
        self::expectException(MissingInputException::class);
        self::expectExceptionMessage('Aborted');

        $this->commandTester->execute([]);
    }

    public function testValidData(): void
    {
        $this->commandTester->setInputs(['Name']);
        $this->commandTester->execute(
            [
                'firstName' => 'Andri',
                'lastName' => 'Voinicu',
                'cnp' => '5010911070069',
                'email' => 'email@email.com',
            ],
        );

        $this->assertStringContainsString(
            'Account was successfully created!',
            $this->commandTester->getDisplay()
        );
    }
}
