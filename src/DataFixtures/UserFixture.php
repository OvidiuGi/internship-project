<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->plainPassword = 'Parola';
        $user->setPassword($this->passwordHasher->hashPassword($user, $user->plainPassword));
        $user->email = 'my.email@server.com';
        $user->setRoles(["ROLE_ADMIN"]);
        $user->firstName = 'Test';
        $user->lastName = 'Testulescu';
        $user->telephoneNr = '123145';
        $user->cnp = '5010911070069';
        $user->setDeletedAt(null);

        $manager->persist($user);
        $manager->flush();
    }
}
