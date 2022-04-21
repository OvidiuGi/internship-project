<?php

namespace App\DataFixtures;

use App\Entity\Building;
use App\Entity\Room;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RoomFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $room = new Room();

        $room->name = 'room1';
        $room->capacity = 10;

        $manager->persist($room);
        $manager->flush();
    }
}
