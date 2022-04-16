<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Programme;
use DateTime;
use PHPUnit\Framework\TestCase;

class ProgrammeTest extends TestCase
{
    public function testCreateProgramme()
    {
        $programme = new Programme();
        $programme->name = 'Test';
        $programme->description = 'test';
        $programme->isOnline = true;
        $programme->maxParticipants = 10;
        $programme->setStartTime(new DateTime('1879-03-14'));
        $programme->setEndTime(new DateTime('1879-03-14'));

        $this->assertEquals('Test', $programme->name);
        $this->assertEquals('test', $programme->description);
        $this->assertTrue($programme->isOnline);
        $this->assertEquals(10, $programme->maxParticipants);
        $this->assertEquals(new DateTime('1879-03-14'), $programme->getEndTime());
        $this->assertEquals(new DateTime('1879-03-14'), $programme->getStartTime());
    }
}
