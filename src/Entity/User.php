<?php

namespace App\Entity;

use App\Controller\Dto\UserDto;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator as MyAssert;

/**
 * @ORM\Entity()
 */
class User implements \JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id = 0;

    /**
     * @ORM\Column(type="string")
     * @Assert\Regex("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/")
     * @MyAssert\Password
     */
    public string $password = '';

    /**
     * @ORM\Column(type="string",size = 13)
     * @Assert\Regex("/^([1-8])([0-9]{2})(0[1-9]|1[0-2])(0[1-9]|[1-2][0-9]|3[0-1])(0[0-9]|[1-3]\d|4[0-8])(\d{3})([0-9])$/")
     * @MyAssert\Cnp
     * @Assert\Length(
     *     min = 13,
     *     max = 13,
     *     exactMessage = "The CNP must have 13 digits!"
     * )
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

    public function jsonSerialize() : array
    {
        return [
            "id" => $this->id,
            "firstName" => $this->firstName,
            "lastName" =>$this->lastName,
            "email" =>$this->email,
            "cnp" =>$this->cnp,
            "roles" =>$this->roles,
        ];
    }

    public static function createFromDto(UserDto $userDto): self
    {
        $user = new self();
        $user->cnp = $userDto->cnp;
        $user->password = $userDto->password;
        $user->email = $userDto->email;
        $user->lastName = $userDto->lastName;
        $user->firstName = $userDto->firstName;

        return $user;
    }
}