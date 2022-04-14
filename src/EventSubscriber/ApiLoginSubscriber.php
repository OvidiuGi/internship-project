<?php

namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class ApiLoginSubscriber implements EventSubscriberInterface
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $analyticsLogger)
    {
        $this->logger = $analyticsLogger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => 'loginSuccessLog',
            LoginFailureEvent::class => 'loginFailedLog'
        ];
    }

    public function loginSuccessLog(LoginSuccessEvent $event)
    {
        $user = $event->getUser();
        $this->logger->info(
            'Logged in successfully',
            [
                'email' => $user->getUserIdentifier(),
                'role' => $user->getRoles()
            ]
        );
    }

    public function loginFailedLog(LoginFailureEvent $event)
    {
        $user = $event->getPassport('user')->getUser()->getUserIdentifier();
        $this->logger->info('Failed logging in', ['email' => $user]);
    }
}
