<?php

namespace App\Entity;

class Room
{
    private int $id;

    public string $name = '';

    public string $description = '';

    public \DateTime $startTime;

    public \DateTIme $endTime;

    public function getId(): int
    {

        returN $this->id;
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
}
