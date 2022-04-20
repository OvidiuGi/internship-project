<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ProgrammeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
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

    private UserRepository $userRepository;

    private SerializerInterface $serializer;

    private EntityManagerInterface $entityManager;

    private int $maxPerPage;

    private int $defaultPage;

    public function __construct(
        ProgrammeRepository $programmeRepository,
        SerializerInterface $serializer,
        int $maxPerPage,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->programmeRepository = $programmeRepository;
        $this->serializer = $serializer;
        $this->maxPerPage = $maxPerPage;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route(methods={"GET"})
     */
    public function showPaginatedFilteredSorted(Request $request): Response
    {
        $header = $request->headers->get('Accept');
        if (!in_array($header, array_keys(self::ACCEPTED_TYPES), true)) {
            return new Response('Bad Accept header!', Response::HTTP_BAD_REQUEST);
        }
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

    /**
     * @Route(methods={"POST"}, path="/join", name="join_programme")
     */
    public function joinProgramme(Request $request): Response
    {
        $programmeId = $request->query->get('id');
        $programme = $this->programmeRepository->findOneBy(['id' => $programmeId]);
        if (null === $programme) {
            $this->logger->info(
                'Failed joining programme because it doesn\'t exist',
                [
                    'programmeId' => $programmeId
                ]
            );

            return new Response('Failed joining! Programme does not exist!', Response::HTTP_NOT_FOUND);
        }

        $userToken = $request->headers->get('X-AUTH-TOKEN');
        $user = $this->userRepository->findOneBy(['apiToken' => $userToken]);
        if (null === $user) {
            $this->logger->info(
                'User with token failed joining to programme',
                [
                    'programmeId' => $programmeId,
                    'userToken' => $userToken,
                ]
            );

            return new Response('Failed joining! User does not exist!', Response::HTTP_NOT_FOUND);
        }

        $programme->addCustomer($user);

        $this->entityManager->flush();

        return new JsonResponse('Successfully joined programme: ' . $programmeId, Response::HTTP_OK, [], true);
    }
}
