<?php

namespace App\Tests\Integration\Message;

use App\Message\SmsNotification;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SmsNotificationTest extends KernelTestCase
{
    private ?SmsNotification $smsNotification;

    protected function setUp(SmsNotification $smsNotification): void
    {
        parent::setUp();
        self::bootKernel();
    }

    public function testGetBody()
    {
        $body = $this->smsNotification->getBody();

        self::assertEquals($body, '');
    }
}
