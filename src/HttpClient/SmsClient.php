<?php

namespace App\HttpClient;

use Psr\Log\LoggerAwareTrait;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SmsClient
{
    use LoggerAwareTrait;

    private HttpClientInterface $client;

    private string $smsUri;

    public function __construct(HttpClientInterface $smsClient, string $smsUri)
    {
        $this->client = $smsClient;
        $this->smsUri = $smsUri;
    }

    public function sendSms(string $receiver, string $body): void
    {
        try {
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
        } catch (BadRequestHttpException $badRequestHttpException) {
            $this->logger->error($badRequestHttpException->getMessage());
        }
    }
}
