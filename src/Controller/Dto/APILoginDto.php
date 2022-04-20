<?php

namespace App\Controller\Dto;

class APILoginDto
{
    private \DateTime $dateTime;

    public array $context = [];

    public string $message;

    public function getDateTime(): \DateTime
    {
        return $this->dateTime;
    }

    public function setDateTime(\DateTime $dateTime): self
    {
        $this->dateTime = $dateTime;

        return $this;
    }
}
