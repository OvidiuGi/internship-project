<?php

namespace App\DataFixtures;

use App\Entity\Building;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BuildingFixture extends Fixture
{

    public function load(ObjectManager $manager)
    {
        $building = new Building();

        $building->setStartTime(new \DateTime(2022 - 04 - 07));
        $building->setEndTime(new \DateTime(2022 - 04 - 07));

        $manager->persist($building);
        $manager->flush();
    }
}
