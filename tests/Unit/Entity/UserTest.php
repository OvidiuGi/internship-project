<?php

namespace App\Tests\Unit\Entity;

use App\Entity\User;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private MockObject $collection;

    public function setUp(): void
    {
        parent::setUp();

        $this->collection = $this->createMock(Collection::class);
    }
    public function testCreateUser(): void
    {
        $user = new User();
        $user->firstName = 'Test';
        $user->lastName = 'Testulescu';
        $user->email = 'test@test.com';
        $user->cnp = '5010911070069';
        $user->plainPassword = 'Parola123';
        $user->telephoneNr = '0754281716';
        $user->setRoles(['ROLE_ADMIN']);
        $user->forgotPasswordToken = '123';
        $user->setApiToken('1234');
        $user->setPassword('123');
        $user->setDeletedAt(null);
        $user->setForgotPasswordTokenTime(null);
        $user->setProgrammes($this->collection);

        $this->assertEquals("Test", $user->firstName);
        $this->assertEquals("Testulescu", $user->lastName);
        $this->assertIsIterable($user->getProgrammes());
        $this->assertEquals("test@test.com", $user->email);
        $this->assertEquals("test@test.com", $user->getUsername());
        $this->assertEquals("5010911070069", $user->cnp);
        $this->assertEquals("Parola123", $user->plainPassword);
        $this->assertEquals("0754281716", $user->telephoneNr);
        $this->assertEquals("ROLE_ADMIN", $user->getRoles()[0]);
        $this->assertEquals("123", $user->forgotPasswordToken);
        $this->assertEquals('123', $user->getPassword());
        $this->assertEquals("1234", $user->getApiToken());
        $this->assertEquals(null, $user->getDeletedAt());
        $this->assertEquals(null, $user->getForgotPasswordTokenTime());
    }
}
