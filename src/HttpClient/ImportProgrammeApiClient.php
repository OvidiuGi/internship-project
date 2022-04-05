<?php

namespace App\HttpClient;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ImportProgrammeApiClient
{
    public string $protocol = 'GET';

    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $importProgrammeApiClient)
    {
        $this->client = $importProgrammeApiClient;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function fetchData(): array
    {
        $response = $this->client->request(
            $this->protocol,
            '/api/sport-programs'
        );
        $fetchedData = $response->toArray();

        return $fetchedData['data'];
    }
}
