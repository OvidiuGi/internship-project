<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity()
 */
class Programme
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"api:programme:all"})
     */
    private int $id;

    /**
     * @ORM\Column(type="string")
     * @Groups({"api:programme:all"})
     */
    public string $name = '';

    /**
     * @ORM\Column(type="text")
     * @Groups({"api:programme:all"})
     */
    public string $description = '';

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"api:programme:all"})
     */
    private DateTime $startTime;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"api:programme:all"})
     */
    private DateTime $endTime;

    /**
     * Many Programmes have One Trainer.
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="trainer_id", referencedColumnName="id")
     * @Groups({"api:programme:all"})
     */
    private ?User $trainer;

    /**
     * Many Programmes have One Room.
     *
     * @ORM\ManyToOne(targetEntity="Room")
     * @ORM\JoinColumn(name="room_id", referencedColumnName="id")
     * @Groups({"api:programme:all"})
     */
    private Room $room;

    /**
     * Many Programmes have Many Users.
     *
     * @ORM\ManyToMany(targetEntity="User", inversedBy="programmes")
     * @ORM\JoinTable(name="programmes_customers")
     */
    private Collection $customers;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"api:programme:all"})
     */
    public bool $isOnline = false;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"api:programme:all"})
     */
    public int $maxParticipants = 0;

    public function __construct()
    {
        $this->customers = new ArrayCollection();
    }

    public static function createFromArray(array $array): self
    {
        $programme = new self();

        $programme->name = $array[0];
        $programme->description = $array[1];
        $programme->setStartTime(\DateTime::createFromFormat('d.m.Y H:i', $array[2]));
        $programme->setEndTime(\DateTime::createFromFormat('d.m.Y H:i', $array[3]));
        $programme->isOnline = filter_var($array[4], FILTER_VALIDATE_BOOLEAN);
        $programme->maxParticipants = (int) $array[5];

        return $programme;
    }

    public function addCustomer(User $customer): self
    {
        if ($this->customers->contains($customer)) {
            return $this;
        }

        $this->customers->add($customer);
        $customer->addProgramme($this);

        return $this;
    }

    public function removeCustomer(User $customer): self
    {
        if (!$this->customers->contains($customer)) {
            return $this;
        }

        $this->customers->removeElement($customer);
        $customer->removeProgramme($this);

        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getStartTime(): DateTime
    {
        return $this->startTime;
    }

    public function setStartTime(DateTime $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): DateTime
    {
        return $this->endTime;
    }

    public function setEndTime(DateTime $endTime): self
    {
        $this->endTime = $endTime;

        return $this;
    }

    public function getTrainer(): ?User
    {
        return $this->trainer;
    }

    public function setTrainer(?User $trainer): self
    {
        $this->trainer = $trainer;

        return $this;
    }

    public function getRoom(): Room
    {
        return $this->room;
    }

    public function setRoom(Room $room): self
    {
        $this->room = $room;

        return $this;
    }

    public function getCustomers(): Collection
    {
        return $this->customers;
    }

    public function setCustomers(Collection $customers): self
    {
        $this->customers = $customers;

        return $this;
    }

    public function assignDataToProgramme(
        string $name,
        string $description,
        DateTime $startTime,
        DateTime $endTime,
        bool $isOnline,
        int $maxParticipants
    ): self {
        $this->name = $name;
        $this->description = $description;
        $this->setStartTime($startTime);
        $this->setEndTime($endTime);
        $this->setTrainer(null);
        $this->isOnline = $isOnline;
        $this->maxParticipants = $maxParticipants;

        return $this;
    }
}
