<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;

class Programme
{
    private int $id;

    public string $name = '';

    public string $description = '';

    private \DateTime $startTime;

    private \DateTime $endTime;

    private ?User $trainer;

    private Room $room;

    private Collection $customers;

    public bool $isOnline = false;

    public function getId(): int
    {
        return $this->id;
    }

    public function getStartTime(): \DateTime
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTime $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): \DateTime
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTime $endTime): self
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
