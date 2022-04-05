<?php

namespace App\HttpClient;

use Psr\Log\LoggerAwareTrait;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SmsClient
{
    use LoggerAwareTrait;

    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $smsClient)
    {
        $this->client = $smsClient;
    }

    public function sendSms(string $receiver, string $body): void
    {
        try {
            $this->client->request(
                'POST',
                '/api/messages',
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
