<?php

namespace App\Entity;

use App\Controller\Dto\UserDto;
use App\Repository\UserRepository;
use App\Validator as MyAssert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use DateTime;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_TRAINER = 'ROLE_TRAINER';

    public const ROLES = ['ROLE_USER', 'ROLE_ADMIN', 'ROLE_TRAINER'];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"api:programme:all"})
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Email()
     */
    public string $email = '';

    /**
     * @ORM\Column(type="json")
     * @Assert\Choice(choices=User::ROLES, multiple=true)
     */
    private array $roles = [];

    /**
     * @ORM\Column(type="string")
     * @MyAssert\Password()
     */
    public string $password = '';

    /**
     * @MyAssert\Password()
     */
    public string $plainPassword;

    /**
     * @ORM\Column(type="string", length=13, options={"fixed" = true})
     * @MyAssert\Cnp()
     * @Assert\NotBlank()
     */
    public string $cnp = '';

    /**
     * @ORM\Column(type="string", unique="true")
     */
    public string $telephoneNr = '';

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\Regex("/^[A-Z][a-z]+$/")
     * @Groups({"api:programme:all"})
     */
    public string $firstName = '';

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\Regex("/^[A-Z][a-z]+$/")
     * @Groups({"api:programme:all"})
     */
    public string $lastName = '';

    /**
     * Many Users have Many Programmes.
     *
     * @ORM\ManyToMany(targetEntity="Programme",mappedBy="customers")
     */
    private Collection $programmes;

    /**
     * @ORM\Column(type="string", unique=true, nullable=true)
     */
    private ?string $apiToken;

    /**
     * @ORM\Column(type="string", unique=true, nullable=true)
     */
    public ?string $forgotPasswordToken;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTime $forgotPasswordTokenTime;

    public function __construct()
    {
        $this->programmes = new ArrayCollection();
    }

    public static function createFromDto(UserDto $userDto): self
    {
        $user = new self();
        $user->cnp = $userDto->cnp;
        $user->plainPassword = $userDto->password;
        $user->email = $userDto->email;
        $user->lastName = $userDto->lastName;
        $user->firstName = $userDto->firstName;
        $user->setRoles($userDto->roles);
        $user->telephoneNr = '';

        return $user;
    }

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return $this->email;
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

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getForgotPasswordTokenTime(): ?DateTime
    {
        return $this->forgotPasswordTokenTime;
    }

    public function setForgotPasswordTokenTime(?DateTime $forgotPasswordTokenTime): self
    {
        $this->forgotPasswordTokenTime = $forgotPasswordTokenTime;

        return $this;
    }

    public function setApiToken(?string $token): self
    {
        $this->apiToken = $token;

        return $this;
    }

    public function getApiToken(): ?string
    {
        return $this->apiToken;
    }
}
