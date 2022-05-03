<?php

namespace App\DataFixtures;

use App\Entity\Programme;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * @codeCoverageIgnore
 */
class ProgrammeFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $programme = new Programme();
        $programme->name = 'Yoga';
        $programme->description = 'test';
        $programme->maxParticipants = 10;
        $programme->isOnline = false;
        $programme->setStartTime(new DateTime('1879-03-14'));
        $programme->setEndTime(new DateTime('1879-03-14'));

        $manager->persist($programme);
        $manager->flush();
    }
}
