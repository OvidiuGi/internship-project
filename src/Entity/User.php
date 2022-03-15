<?php

namespace App\Entity;

use App\Controller\Dto\UserDto;
use Doctrine\ORM\Mapping as ORM;
use App\Validator as MyAssert;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity()
 */
class User
{
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_TRAINER = 'ROLE_TRAINER';

    public const ROLES = ['ROLE_USER', 'ROLE_ADMIN', 'ROLE_TRAINER'];

    /**
     * @ORM\Column(type="string")
     * @MyAssert\Password()
     */
    public string $password = '';

    /**
     * @ORM\Column(type="string", length=13)
     * @MyAssert\Cnp()
     * @Assert\NotBlank()
     */
    public string $cnp = '';

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Email()
     */
    public string $email = '';

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\Regex("/^[A-Z][a-z]+$/")
     */
    public string $firstName = '';

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\Regex("/^[A-Z][a-z]+$/")
     */
    public string $lastName = '';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="json")
     * @Assert\Choice(choices=User::ROLES, multiple=true)
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

//    public function addProgramme(Programme $programme): self
//    {
//        if ($this->programmes->contains($programme)) {
//            return $this;
//        }
//        $this->programmes->add($programme);
//        $programme->addCustomer($this);
//
//        return $this;
//    }
//
//    public function removeProgramme(Programme $programme): self
//    {
//        if (!$this->programmes->contains($programme)) {
//            return $this;
//        }
//
//        $this->programmes->removeElement($programme);
//        $programme->removeCustomer($this);
//
//        return $this;
//    }

    public static function createFromDto(UserDto $userDto): self
    {
        $user = new self();
        $user->cnp = $userDto->cnp;
        $user->password = $userDto->password;
        $user->email = $userDto->email;
        $user->lastName = $userDto->lastName;
        $user->firstName = $userDto->firstName;
        $user->setRoles($userDto->roles);

        return $user;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;

        $roles[] = self::ROLE_USER;

        return array_values(array_unique($roles));
    }

    public function setRoles(array $roles): self
    {
        $this->roles = array_values(array_unique($roles));

        return $this;
    }

    public function getProgrammes(): Collection
    {
        return $this->programmes;
    }

    public function setProgrammes(Collection $programmes): self
    {
        $this->programmes = $programmes;

        return $this;
    }
}
