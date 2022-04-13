<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class ResponseSubscriber implements EventSubscriberInterface
{
    private string $apiVersion;

    public function __construct(string $apiVersion)
    {
        $this->apiVersion = $apiVersion;
    }

    public static function getSubscribedEvents(): array
    {
        return [ResponseEvent::class => 'addApiVersionHeader'];
    }

    public function addApiVersionHeader(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $route = $event->getRequest()->attributes->get('_route');
        if (null === $route) {
            return;
        }
        if (strpos($route, 'api') === false) {
            return;
        }

        $response = $event->getResponse();

        $response->headers->set("X-API-VERSION", $this->apiVersion);
    }
}
