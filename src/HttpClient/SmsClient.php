<?php

namespace App\HttpClient;

use Psr\Log\LoggerAwareTrait;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SmsClient
{
    use LoggerAwareTrait;

    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $smsClient)
    {
        $this->client = $smsClient;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ClientException
     */
    public function sendSms(string $receiver, string $body): void
    {
        try {
            $this->client->request(
                REQUEST::METHOD_POST,
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
