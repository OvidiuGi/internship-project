<?php

namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class LoginSubscriber implements EventSubscriberInterface
{
    private LoggerInterface $logger;

    public const FIREWALL_TYPES = ['admin', 'api'];

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

    public function loginSuccessLog(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();
        $firewall = $event->getFirewallName();
        if (!\in_array($firewall, self::FIREWALL_TYPES)) {
            return;
        }

        $this->logger->info(
            'Logged in successfully',
            [
                'email' => $user->getUserIdentifier(),
                'role' => $user->getRoles()[0],
                'type' => 'login',
                'firewall' => $firewall,
                'success' => true
            ]
        );
    }

    public function loginFailedLog(LoginFailureEvent $event): void
    {
        $user = $event->getPassport()->getUser()->getUserIdentifier();
        $firewall = $event->getFirewallName();
        if (!\in_array($firewall, self::FIREWALL_TYPES)) {
            return;
        }

        $this->logger->info(
            'Failed logging in',
            [
                'email' => $user,
                'type' => 'login',
                'firewall' => $firewall,
                'success' => false
            ]
        );
    }
}
