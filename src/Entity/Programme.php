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

    public function getStartTime() : \DateTime
    {

        return $this->startTime;
    }

    public function setStartTime(\DateTime $startTime): \DateTime
    {

        $this->startTime = $startTime;

        return $this->startTime;
    }

    public function getEndTime() : \DateTime
    {

        return $this->endTime;
    }

    public function setEndTime(\DateTime $endTime): \DateTime
    {

        $this->endTime = $endTime;

        return $this->endTime;
    }

    public function getTrainer() : ?User
    {

        return $this->trainer;
    }

    public function setTrainer(?User $trainer) : ?User
    {
        $this->trainer = $trainer;

        return $this->trainer;
    }

    public function getRoom() : ?Room
    {

        return $this->room;
    }

    public function setRoom(?Room $room) : ?Room
    {
        $this->room = $room;

        return $this->room;
    }

    public function getCustomers() : Collection
    {

        return $this->customers;
    }

    public function setCustomers(Collection $customers) : Collection
    {
        $this->customers = $customers;

        return $this->customers;
    }
}

