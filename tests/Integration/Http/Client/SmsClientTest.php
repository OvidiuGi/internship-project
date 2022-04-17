<?php

namespace App\Tests\Integration\Http\Client;

use App\HttpClient\SmsClient;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class SmsClientTest extends KernelTestCase
{
    private ?SmsClient $client;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $container = static::getContainer();

        $this->client = $container->get(SmsClient::class);
    }

    public function testSendSms(): void
    {
        $this->client->sendSms('0754281716', 'Test');

        self::assertTrue(true);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testWrongReceiver(): void
    {
        self::expectException(ClientException::class);
        self::expectExceptionMessage(
            'HTTP/1.1 400 Bad Request returned for'
        );

        $this->client->sendSms('test', 'Test');
    }
}
