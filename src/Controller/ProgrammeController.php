<?php

namespace App\Controller;

use App\Repository\ProgrammeRepository;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route(path="/api/programme")
 */
class ProgrammeController implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private ProgrammeRepository $programmeRepository;

    private SerializerInterface $serializer;

    public function __construct(ProgrammeRepository $programmeRepository, SerializerInterface $serializer)
    {
        $this->programmeRepository = $programmeRepository;
        $this->serializer = $serializer;
    }

    /**
     * @Route(methods={"GET"})
     */
    public function showAll(): Response
    {
        $programme = $this->programmeRepository->getAll();

        $serializedProgramme = $this->serializer->serialize($programme, 'json', ['groups' => 'api:programme:all']);

        return new JsonResponse($serializedProgramme, Response::HTTP_CREATED);
    }
}
