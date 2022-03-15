<?php

namespace App\Controller\Dto;

use App\Entity\User;

class UserDto
{
    public int $id;

    public string $firstName;

    public string $lastName;

    public string $email;

    public string $password;

    public string $cnp;

    public array $roles;

    public static function createFromUser(User $user): self
    {
        $dto = new self();
        $dto->id = $user->getId();
        $dto->roles = ['customer'];
        $dto->lastName = $user->lastName;
        $dto->firstName = $user->firstName;
        $dto->email = $user->email;
        $dto->cnp = $user->cnp;
        $dto->password = $user->password;

        return $dto;
    }
}