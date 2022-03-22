<?php

namespace App\Controller;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/api/programme)
 */
class ProgrammeController implements LoggerAwareInterface
{
    use LoggerAwareTrait;
}
