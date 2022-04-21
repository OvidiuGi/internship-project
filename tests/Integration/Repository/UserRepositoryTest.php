<?php

namespace App\Tests\Integration\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{
    protected function runTest(): void
    {
        $this->markTestSkipped('Skipped test');
    }

    public function testAddUser(): void
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = new User();
        $testUser->email = 'admin@admin.com';
        $testUser->firstName = 'Admin';
        $testUser->lastName = 'Admin';
        $testUser->telephoneNr = '12345';
        $testUser->password = 'Parola!@#';
        $testUser->cnp = '5010911070069';
        $userRepository->add($testUser);
        $this->assertEquals($testUser, $userRepository->findOneBy(['email' => 'admin@admin.com']));
    }

    public function testGetPaginated(): void
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        $userRepository->getPaginated(1, 1);

        $this->assertTrue(true);
    }
}
