<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Building;
use DateTime;
use PHPUnit\Framework\TestCase;

class BuildingTest extends TestCase
{
    public function testCreateBuilding(): void
    {
        $building = new Building();
        $building->setStartTime(new DateTime('1879-03-14'));
        $building->setEndTime(new DateTime('1879-03-14'));
        $this->assertEquals(new DateTime('1879-03-14'), $building->getStartTime());
        $this->assertEquals(new DateTime('1879-03-14'), $building->getEndTime());
    }
}
