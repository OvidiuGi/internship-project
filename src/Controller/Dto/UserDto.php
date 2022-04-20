<?php

namespace App\Controller\Dto;

use App\Entity\User;
use App\Validator as MyAssert;
use DateTime;
use Symfony\Component\Validator\Constraints as Assert;

class UserDto
{
    public int $id;

    public string $firstName;

    public string $lastName;

    public string $email;

    /**
     * @MyAssert\Password
     */
    public string $password;

    /**
     * @Assert\IdenticalTo(propertyPath="password", message="Please enter the same password")
     */
    public string $confirmPassword;

    public string $cnp;

    public array $roles = [];

    public string $telephoneNr = '';

    public static function createFromUser(User $user): self
    {
        $dto = new self();
        $dto->id = $user->getId();
        $dto->roles = $user->getRoles();
        $dto->lastName = $user->lastName;
        $dto->firstName = $user->firstName;
        $dto->email = $user->email;
        $dto->cnp = $user->cnp;
        $dto->password = $user->password;
        $dto->telephoneNr = $user->telephoneNr;

        return $dto;
    }
}
