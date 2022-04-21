<?php

namespace App\Analytics;

use App\Controller\Dto\AnalyticsDto;
use Symfony\Component\Serializer\SerializerInterface;

class LogParser
{
    private string $analyticsLog;

    private SerializerInterface $serializer;

    public function __construct(string $analyticsLog, SerializerInterface $serializer)
    {
        $this->analyticsLog = $analyticsLog;

        $this->serializer = $serializer;
    }

    public function parseLogs(): AnalyticsCollection
    {
        $analytics = new AnalyticsCollection();

        $handler = \fopen($this->analyticsLog, 'r', true);
        $line = \fgets($handler);
        while ($line != null) {
            $deserializedData = $this->serializer->deserialize($line, AnalyticsDto::class, 'json');
            $analytics->addToCollection($deserializedData);
            $line = \fgets($handler);
        }

        return $analytics;
    }
}
