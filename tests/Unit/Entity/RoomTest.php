<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Building;
use App\Entity\Room;
use PHPUnit\Framework\TestCase;

class RoomTest extends TestCase
{
    public function testCreateRoom()
    {
        $room = new Room();
        $room->name = 'test';
        $room->capacity = 10;
        $room->setBuilding(new Building());

        $this->assertEquals('test', $room->name);
        $this->assertEquals(10, $room->capacity);
        $this->assertEquals(new Building(), $room->getBuilding());
    }
}
