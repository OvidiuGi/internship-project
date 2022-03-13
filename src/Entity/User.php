<?php

namespace App\Entity;

use App\Controller\Dto\UserDto;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use App\Validator as MyAssert;

/**
 * @ORM\Entity()
 */
class User
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string")
     * @Assert\Regex("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/")
     * @MyAssert\Password
     */
    public string $password = '';

    /**
     * @ORM\Column(type="string", length=13)
     * @MyAssert\Cnp
     * @Assert\NotBlank
     */
    public string $cnp = '';

    /**
     * @ORM\Column(type="string")
     * @Assert\Email
     */
    public string $email = '';

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank
     * @Assert\Regex("/^[A-Z][a-z]+$/")
     */
    public string $firstName = '';

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank
     * @Assert\Regex("/^[A-Z][a-z]+$/")
     */
    public string $lastName = '';

    /**
     * @ORM\Column(type="json")
     */
    private array $roles = [];

    /**
     * Many Users have Many Programmes
     * @ORM\ManyToMany(targetEntity="Programme",mappedBy="customers")
     */
    private Collection $programmes;

    public function __construct()
    {
        $this->programmes = new ArrayCollection();
    }

    public function addProgramme(Programme $programme): self
    {
        if ($this->programmes->contains($programme)) {
            return $this;
        }
        $this->programmes->add($programme);
        $programme->addCustomer($this);

        return $this;
    }

    public function removeProgramme(Programme $programme): self
    {
        if (!$this->programmes->contains($programme)) {
            return $this;
        }

        $this->programmes->removeElement($programme);
        $programme->removeCustomer($this);

        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getRoles(): array
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

    public static function createFromDto(UserDto $userDto): self
    {
        $user = new self();
        $user->cnp = $userDto->cnp;
        $user->password = $userDto->password;
        $user->email = $userDto->email;
        $user->lastName = $userDto->lastName;
        $user->firstName = $userDto->firstName;
        $user->setRoles(['customer']);

        return $user;
    }
}