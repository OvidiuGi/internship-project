<?php

namespace App\Analytics;

use App\Controller\Dto\APILoginDto;
use App\Entity\User;
use Symfony\Component\Serializer\SerializerInterface;

class APILoginsParser
{
    private string $analyticsLog;

    private SerializerInterface $serializer;

    public function __construct(string $analyticsLog, SerializerInterface $serializer)
    {
        $this->analyticsLog = $analyticsLog;
        $this->serializer = $serializer;
    }

    public function getAPILogins()
    {
        $handler = \fopen($this->analyticsLog, 'r', true);
        $line = \fgets($handler);
        while ($line != null) {
            $deserializedData = $this->serializer->deserialize($line, APILoginDto::class, 'json');

            $line = \fgets($handler);
        }
    }
}
