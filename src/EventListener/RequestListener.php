<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ResponseEvent;

class RequestListener
{
    private string $version;

    public function __construct(string $version)
    {
        $this->version = $version;
    }

    public function onKernelResponse(ResponseEvent $event)
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $response = $event->getResponse();

        $response->headers->set("X-API-VERSION", $this->version);
    }
}