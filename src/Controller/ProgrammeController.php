<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\ProgrammeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route(path="/api/programmes")
 */
class ProgrammeController extends AbstractController implements LoggerAwareInterface
{
    use LoggerAwareTrait;

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

    /**
     * @Route(methods={"POST"}, path="/{id}/customers", name="join_programme")
     */
    public function joinProgramme(int $id, Request $request): Response
    {
        $programme = $this->programmeRepository->findOneBy(['id' => $id]);
        if (null === $programme) {
            $this->logger->info(
                'Failed joining programme because it doesn\'t exist',
                [
                    'programmeId' => $id
                ]
            );

            return new Response('Failed joining! Programme does not exist!', Response::HTTP_NOT_FOUND);
        }

        $userToken = $request->headers->get('X-AUTH-TOKEN');
        /** @var User $user */
        $user = $this->getUser();
        if (null === $user) {
            $this->logger->info(
                'User with token failed joining to programme',
                [
                    'programmeId' => $id,
                    'userToken' => $userToken,
                ]
            );

            return new Response('Failed joining! User does not exist!', Response::HTTP_NOT_FOUND);
        }

        $programme->addCustomer($user);

        $this->entityManager->flush();

        return new JsonResponse('Successfully joined programme: ' . $id, Response::HTTP_OK, [], true);
    }
}
