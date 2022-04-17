<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Programme;
use App\Entity\Room;
use App\Entity\User;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ProgrammeTest extends TestCase
{
    private MockObject $trainer;

    private MockObject $room;

    private MockObject $collection;

    public function setUp(): void
    {
        parent::setUp();

        $this->trainer = $this->createMock(User::class);

        $this->room = $this->createMock(Room::class);

        $this->collection = $this->createMock(Collection::class);
    }

    public function testCreateProgramme(): void
    {
        $programme = new Programme();
        $programme->name = 'Test';
        $programme->description = 'test';
        $programme->isOnline = true;
        $programme->maxParticipants = 10;
        $programme->setStartTime(\DateTime::createFromFormat('d.m.Y H:i', '17.03.2022 16:00'));
        $programme->setEndTime(\DateTime::createFromFormat('d.m.Y H:i', '17.03.2022 18:00'));
        $programme->setTrainer($this->trainer);
        $programme->setRoom($this->room);
        $programme->addCustomer($this->trainer);
        $programme->setCustomers($this->collection);
        $programme->removeCustomer($this->trainer);

        $this->assertEquals('Test', $programme->name);
        $this->assertEquals('test', $programme->description);
        $this->assertTrue($programme->isOnline);
        $this->assertEquals(10, $programme->maxParticipants);
        $this->assertEquals(\DateTime::createFromFormat('d.m.Y H:i', '17.03.2022 16:00'), $programme->getStartTime());
        $this->assertEquals(\DateTime::createFromFormat('d.m.Y H:i', '17.03.2022 18:00'), $programme->getEndTime());
        $this->assertIsObject($programme->getTrainer());
        $this->assertIsObject($programme->getCustomers());
        $this->assertIsObject($programme->getRoom());
    }

    public function testCreateFromArray(): void
    {
        $data = [
            'name',
            'description',
            '17.03.2022 16:00',
            '17.03.2022 18:00',
            'false',
            10
        ];
        $programme = new Programme();
        $programme = $programme->createFromArray($data);
        $this->assertIsObject($programme);
    }
}
