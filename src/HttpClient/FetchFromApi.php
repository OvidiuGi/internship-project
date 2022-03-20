<?php

namespace App\HttpClient;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class FetchFromApi
{
    public string $url = 'http://evozon-internship-data-wh.herokuapp.com/api/sport-programs';

    public string $protocol = 'GET';

    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function fetchData(): array
    {
        $response = $this->client->request(
            $this->protocol,
            $this->url
        );
        $fetchedData = $response->toArray();

        return $fetchedData['data'];
    }
}