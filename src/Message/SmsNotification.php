<?php

namespace App\Message;

class SmsNotification
{
    private string $body;

    private string $receiver;

    public function __construct(string $receiver, string $body)
    {
        $this->body = $body;
        $this->receiver = $receiver;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getReceiver(): string
    {
        return $this->receiver;
    }
}