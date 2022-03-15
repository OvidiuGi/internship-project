<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Room
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
     * @ORM\Column(type="integer")
     */
    public int $capacity = 0;

    /**
     * Many Rooms have One Building
     * @ORM\ManyToOne(targetEntity="Building")
     * @ORM\JoinColumn(name="building_id", referencedColumnName="id")
     */
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
