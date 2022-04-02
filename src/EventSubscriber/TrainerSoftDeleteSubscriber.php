<?php

namespace App\EventSubscriber;

use App\Event\TrainerSoftDeleteEvent;
use App\Repository\ProgrammeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TrainerSoftDeleteSubscriber implements EventSubscriberInterface
{
    use LoggerAwareTrait;

    private UserRepository $userRepository;

    private ProgrammeRepository $programmeRepository;

    private EntityManagerInterface $entityManager;

    public function __construct(
        UserRepository $userRepository,
        ProgrammeRepository $programmeRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->userRepository = $userRepository;
        $this->programmeRepository = $programmeRepository;
        $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TrainerSoftDeleteEvent::NAME => [
                ['updateProgrammeOfTrainer', 9],
                ['softDeleteTrainer', 8],
            ],
        ];
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function softDeleteTrainer(TrainerSoftDeleteEvent $event): void
    {
        $user = $event->getTrainer();
        $this->userRepository->remove($user);
    }

    public function updateProgrammeOfTrainer(TrainerSoftDeleteEvent $event): void
    {
        $trainer = $event->getTrainer();
        $this->programmeRepository->removeTrainerById($trainer->getId());
    }
}
