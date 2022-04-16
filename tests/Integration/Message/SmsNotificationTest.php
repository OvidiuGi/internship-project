<?php

namespace App\Tests\Integration\Message;

use App\Message\SmsNotification;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SmsNotificationTest extends KernelTestCase
{
    private ?SmsNotification $smsNotification;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $this->smsNotification = new SmsNotification('07231', 'test');
    }

    public function testGetBody()
    {
        $body = $this->smsNotification->getBody();

        self::assertEquals('test', $body);
    }

    public function testGetReceiver()
    {
        $receiver = $this->smsNotification->getReceiver();

        self::assertEquals('07231', $receiver);
    }
}
