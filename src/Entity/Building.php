<?php

namespace App\Entity;

class Building
{
    private int $id;

    public \DateTime $starTime;

    public \DateTime $endTime;

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
}
