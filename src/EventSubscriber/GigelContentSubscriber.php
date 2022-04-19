<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\Serializer\SerializerInterface;

class GigelContentSubscriber implements EventSubscriberInterface
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ViewEvent::class => 'encodeResponseData',
        ];
    }

    public function encodeResponseData(ViewEvent $event): void
    {
        $accept = $event->getRequest()->headers->get('Accept');
        if ('application/gigel' === $accept) {
            $event->setResponse(
                new JsonResponse(
                    $this->serializer->serialize(
                        $event->getControllerResult(),
                        'gigel',
                        ['groups' => 'api:programme:all']
                    ),
                    Response::HTTP_OK,
                    [],
                    true
                )
            );
        }

        if ('application/json' === $accept) {
            $event->setResponse(
                new JsonResponse(
                    $this->serializer->serialize(
                        $event->getControllerResult(),
                        'json',
                        ['groups' => 'api:programme:all']
                    ),
                    Response::HTTP_OK,
                    [],
                    true
                )
            );
        }

        if ('application/xml' === $accept) {
            $event->setResponse(
                new JsonResponse(
                    $this->serializer->serialize(
                        $event->getControllerResult(),
                        'xml',
                        ['groups' => 'api:programme:all']
                    ),
                    Response::HTTP_OK,
                    [],
                    true
                )
            );
        }
    }
}
