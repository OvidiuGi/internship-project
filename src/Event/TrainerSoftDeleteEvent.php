<?php

namespace App\Event;

use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class TrainerSoftDeleteEvent extends Event
{
    public const NAME = 'trainer.soft.delete';

    protected User $trainer;

    public function __construct(User $trainer)
    {
        $this->trainer = $trainer;
    }

    public function getTrainer(): User
    {
        return $this->trainer;
    }
}
