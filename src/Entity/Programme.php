<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use DateTime;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity()
 */
class Programme
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
    * @ORM\Column(type="string")
    */
    public string $name = '';

    /**
     * @ORM\Column(type="string")
     */
    public string $description = '';

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTime $startTime;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTime $endTime;

    /**
     * Many Programmes have One Trainer
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="trainer_id", referencedColumnName="id")
     */
    private ?User $trainer;

    /**
     * Many Programmes have One Room
     * @ORM\ManyToOne(targetEntity="Room")
     * @ORM\JoinColumn(name="room_id", referencedColumnName="id")
     */
    private Room $room;

    /**
     * Many Programmes have Many Users
     * @ORM\ManyToMany(targetEntity="User", inversedBy="programmes")
     * @ORM\JoinTable(name="programmes_customers")
     */
    private Collection $customers;

    /**
     * @ORM\Column(type="boolean")
     */
    public bool $isOnline = false;

    public function __construct()
    {
        $this->customers = new ArrayCollection();
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
}

