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

    private const ACCEPTED_TYPES =
        [
            'application/json' => 'json',
            'application/xml' => 'xml',
        ];

    private ProgrammeRepository $programmeRepository;

    private SerializerInterface $serializer;

    private int $maxPerPage;

    public function __construct(
        ProgrammeRepository $programmeRepository,
        SerializerInterface $serializer,
        int $maxPerPage
    ) {
        $this->programmeRepository = $programmeRepository;
        $this->serializer = $serializer;
        $this->maxPerPage = $maxPerPage;
    }

    /**
     * @Route(methods={"GET"})
     */
    public function showPaginatedFilteredSorted(Request $request): Response
    {
        $header = $request->headers->get('Accept');
        if (!in_array($header, array_keys(self::ACCEPTED_TYPES), true)) {
            return new Response('', Response::HTTP_BAD_REQUEST);
        }
        $paginate = [];
        $paginate['currentPage'] = $request->query->get('page', 1);
        $paginate['maxPerPage'] = $request->query->get('size', $this->maxPerPage);

        $filters = [];
        $filters['name'] = $request->query->get('name', '');
        $filters['id'] = $request->query->get('id', '');
        $filters['isOnline'] = $request->query->get('isOnline', '');
        if ('' !== $filters['isOnline']) {
            $filters['isOnline'] = $request->query->getBoolean('isOnline');
        }

        $sortBy = $request->query->get('sortBy', '');
        $direction = $request->query->get('sortType', '');

        $resultedProgrammes = $this->programmeRepository->getPaginatedFilteredSorted(
            $paginate,
            $filters,
            $sortBy,
            $direction
        );

        $serializedData = $this->serializer->serialize(
            $resultedProgrammes,
            self::ACCEPTED_TYPES[$header],
            ['groups' => 'api:programme:all']
        );

        return new JsonResponse($serializedData, Response::HTTP_OK, [], true);
    }
}
