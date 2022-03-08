<?php

namespace App\Entity;

class Room
{
    private int $id;

    public string $name = '';

    public string $description = '';

    private \DateTime $startTime;

    private \DateTIme $endTime;

    public function getId(): int
    {
        returN $this->id;
    }

    public function getStartTime() : \DateTime
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTime $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime() : \DateTime
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTime $endTime): self
    {
        $this->endTime = $endTime;

        return $this;
    }
}
