<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Repository\ProgrammeRepository;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Gedmo\SoftDeleteable\SoftDeleteableListener;
use Psr\Log\LoggerAwareTrait;

class UserSoftDeleteSubscriber implements EventSubscriberInterface
{
    use LoggerAwareTrait;

    private EntityManagerInterface $entityManager;

    private ProgrammeRepository $programmeRepository;

    public function __construct(
        ProgrammeRepository $programmeRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->programmeRepository = $programmeRepository;
        $this->entityManager = $entityManager;
    }

    public function getSubscribedEvents(): array
    {
        return [SoftDeleteableListener::POST_SOFT_DELETE];
    }

    public function postSoftDelete(LifecycleEventArgs $args): void
    {
        $user = $args->getObject();
        if (!$user instanceof User) {
            return;
        }
        if (!in_array('ROLE_TRAINER', $user->getRoles(), true)) {
            return;
        }

        $programmes = $this->programmeRepository->removeTrainerByIdFromProgrammes($user->getId());
        foreach ($programmes as $programme) {
            $programme->setTrainer(null);
        }
        $this->entityManager->flush();
    }
}
