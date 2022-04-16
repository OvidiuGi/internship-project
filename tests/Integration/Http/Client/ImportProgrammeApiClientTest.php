<?php

namespace App\Tests\Integration\Http\Client;

use App\HttpClient\ImportProgrammeApiClient;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ImportProgrammeApiClientTest extends KernelTestCase
{
    private ?ImportProgrammeApiClient $client;

    protected function setUp(): void
    {
        parent::setUp();

        $container = self::getContainer();
        $this->client = $container->get(ImportProgrammeApiClient::class);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testFetchData()
    {
        $this->client->fetchData();

        self::assertTrue(true);
    }
}
