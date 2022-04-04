<?php

namespace App\HttpClient;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class SmsClient
{
    private HttpClientInterface $client;

    private string $smsUri;

    public function __construct(HttpClientInterface $smsClient, string $smsUri)
    {
        $this->client = $smsClient;
        $this->smsUri = $smsUri;
    }

    public function sendSms(string $receiver, string $body): void
    {
        $this->client->request(
            'POST',
            $this->smsUri,
            [
                'json' => [
                    'receiver' => $receiver,
                    'body' => $body,
                ],
            ]
        );
    }
}
