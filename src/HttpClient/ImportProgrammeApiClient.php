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
    public string $importFromApiUri;

    public string $protocol = 'GET';

    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $importProgrammeApiClient, string $importFromApiUri)
    {
        $this->client = $importProgrammeApiClient;
        $this->importFromApiUri = $importFromApiUri;
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
            $this->importFromApiUri
        );
        $fetchedData = $response->toArray();

        return $fetchedData['data'];
    }
}
