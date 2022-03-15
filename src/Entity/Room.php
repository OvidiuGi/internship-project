<?php

namespace App\Entity;

class Room
{
    private int $id;

    public string $name = '';

    public int $capacity = 0;

    private Building $building;

    public function getId(): int
    {
        return $this->id;
    }

    public function getBuilding(): Building
    {
        return $this->building;
    }

    public function setBuilding(Building $building): self
    {
        $this->building = $building;

        return $this;
    }
}
