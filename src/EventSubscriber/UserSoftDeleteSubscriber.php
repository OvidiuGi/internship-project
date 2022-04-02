<?php

namespace App\EventSubscriber;

use App\Event\TrainerSoftDeleteEvent;
use App\Event\UserSoftDeleteEvent;
use App\Repository\UserRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserSoftDeleteSubscriber implements EventSubscriberInterface
{
    use LoggerAwareTrait;

    private EventDispatcherInterface $eventDispatcher;

    private UserRepository $userRepository;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        UserRepository $userRepository
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->userRepository = $userRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserSoftDeleteEvent::NAME => [
                ['checkIfTrainer', 10],
                ['softDeleteUser', 9],
            ],
        ];
    }

    public function checkIfTrainer(UserSoftDeleteEvent $event): void
    {
        $user = $event->getUser();
        if (in_array('ROLE_TRAINER', $user->getRoles())) {
            $this->eventDispatcher->dispatch(new TrainerSoftDeleteEvent($user), TrainerSoftDeleteEvent::NAME);
            $event->stopPropagation();
        }
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function softDeleteUser(UserSoftDeleteEvent $event): void
    {
        $user = $event->getUser();
        $this->userRepository->remove($user);
    }
}
