<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ProgrammeRepository;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route(path="/api/programmes")
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

//        var_dump($partial);
//        die;
        $serializedData = $this->serializer->serialize($programme, 'json', ['groups' => 'api:programme:all']);

        return new JsonResponse($serializedData, Response::HTTP_OK, [], true);
    }

    /**
     * @Route(path="/filter/partial", methods={"GET"})
     */
    public function showPartialSearchByName(Request $request): Response
    {
        $nameForSearch = $request->query->get('name');

        $partial = $this->programmeRepository->partialSearchByName($nameForSearch);

        $serializedData = $this->serializer->serialize($partial, 'json', ['groups' => 'api:programme:all']);

        return new JsonResponse($serializedData, Response::HTTP_OK, [], true);
    }

    /**
     * @Route(path="/filter/exact", methods={"GET"})
     */
    public function showExactSearchByName(Request $request): Response
    {
        $nameForSearch = $request->query->get('name');

        $partial = $this->programmeRepository->exactSearchByName($nameForSearch);

        $serializedData = $this->serializer->serialize($partial, 'json', ['groups' => 'api:programme:all']);

        return new JsonResponse($serializedData, Response::HTTP_OK, [], true);
    }
}
