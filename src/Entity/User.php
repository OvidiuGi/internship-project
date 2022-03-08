<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;

class User
{
    private int $id;

    public string $password = '';

    public string $cnp = '';

    public string $email = '';

    public string $firstName = '';

    public string $lastName = '';

    private Collection $roles;

    private Collection $programmes;

    public function getId(): int
    {
        return $this->id;
    }

    public function setRoles(Collection $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function setProgrammes(Collection $programmes): self
    {
        $this->programmes = $programmes;

        return $this;
    }

    public function getProgrammes(): Collection
    {
        return $this->programmes;
    }
}