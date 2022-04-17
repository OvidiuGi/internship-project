<?php

namespace App\Tests\Integration\Controller;

use App\Controller\Dto\UserDto;
use App\Controller\UserController;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserControllerTest extends KernelTestCase
{
    private ?UserController $userController;

    private ?UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $container = static::getContainer();
        $this->userController = $container->get(UserController::class);
        $this->userRepository = $container->get(UserRepository::class);
    }
    public function testRegister(): void
    {
        $userDto = new UserDto();
        $userDto->firstName = 'Test';
        $userDto->lastName = 'Testu';
        $userDto->cnp = '5010911070069';
        $userDto->telephoneNr = '075421';
        $userDto->email = 'email@email.com';
        $userDto->password = 'Parola!@#';
        $userDto->confirmPassword = 'Parola!@#';
        $userDto->roles = ["ROLE_ADMIN"];

        $this->userController->register($userDto);

        self::assertTrue(true);
    }

    public function testRegisterNoData(): void
    {
        self::expectError();
        self::expectErrorMessage(
            'Typed property App\Controller\Dto\UserDto::$cnp must not be accessed before initialization'
        );
        $userDto = new UserDto();

        $this->userController->register($userDto);
    }

    public function testDeleteUser(): void
    {
        $user = $this->userRepository->findAll()[0];
        $this->userController->softDelete($user->getId());

        self::assertNotNull($user->getDeletedAt());
    }

    public function testDeleteInvalidUser(): void
    {
        $this->userController->softDelete(100);
        $user = $this->userRepository->findOneBy(['id' => 100]);
        self::assertEquals(null, $user);
    }
}
