<?php

namespace App\Tests\Command;

use App\Controller\Api\UserController;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Exception\MissingInputException;
use Symfony\Component\Console\Tester\CommandTester;

class CreateAccountCommandTest extends KernelTestCase
{
    protected function runTest(): void
    {
        $this->markTestSkipped('Skipped test');
    }

    private CommandTester $commandTester;

    private UserRepository $userRepository;

    public function setUp(): void
    {
        parent::setUp();

        $kernel = self::bootKernel();

        $application = new Application($kernel);

        $container = static::getContainer();

        $this->userRepository = $container->get(UserRepository::class);

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
                'telephoneNr' => '0754281716'
            ],
        );

        $this->assertStringContainsString(
            'Account was successfully created!',
            $this->commandTester->getDisplay()
        );
        $newUser = $this->userRepository->findOneBy(['email' => 'email@email.com']);
        $this->assertIsObject($newUser);
        $this->assertEquals('Andri', $newUser->firstName);
        $this->assertEquals('Voinicu', $newUser->lastName);
        $this->assertEquals('5010911070069', $newUser->cnp);
        $this->assertEquals('0754281716', $newUser->telephoneNr);
    }
}
