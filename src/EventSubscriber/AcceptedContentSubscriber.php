<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\Serializer\SerializerInterface;

class AcceptedContentSubscriber implements EventSubscriberInterface
{
    private SerializerInterface $serializer;

    public const HEADER_TO_FORMAT_MAP = [
        'application/json' => 'json',
        'application/xml' => 'xml',
        'application/gigel' => 'gigel'
    ];

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

        if (!in_array($accept, array_keys(self::HEADER_TO_FORMAT_MAP))) {
            return;
        }

        $event->setResponse(
            new JsonResponse(
                $this->serializer->serialize(
                    $event->getControllerResult(),
                    self::HEADER_TO_FORMAT_MAP[$accept],
                    ['groups' => 'api:programme:all']
                ),
                Response::HTTP_OK,
                [],
                true
            )
        );
    }
}
