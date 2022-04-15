<?php

namespace App\MessageHandler;

use App\HttpClient\SmsClient;
use App\Message\SmsNotification;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class SmsNotificationHandler implements MessageHandlerInterface
{
    private SmsClient $client;

    public function __construct(SmsClient $smsClient)
    {
        $this->client = $smsClient;
    }

    public function __invoke(SmsNotification $message)
    {
        $this->client->sendSms($message->getReceiver(), $message->getBody());
    }
}
