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

    private int $maxPerPage;

    private int $defaultPage;

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
    public function showPaginatedFilteredSorted(Request $request): array
    {
        $paginate = [];
        $paginate['page'] = $request->query->get('page', 1);
        $paginate['size'] = $request->query->get('size', $this->maxPerPage);

        $filters = [];
        $filters['name'] = $request->query->get('name');
        $filters['id'] = $request->query->get('id');
        $filters['isOnline'] = $request->query->getBoolean('isOnline');
        $filters['description'] = $request->query->get('description');
        $filters['maxParticipants'] = $request->query->get('maxParticipants');

        $sortBy = $request->query->get('sortBy');
        $direction = $request->query->get('direction');

        return $this->programmeRepository->getPaginatedFilteredSorted($paginate, $filters, $sortBy, $direction);
    }
}
