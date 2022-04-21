<?php

namespace App\Controller;

use App\Analytics\LogParser;
use Symfony\Component\Uid\Uuid;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/api")
 */
class ApiLoginController extends AbstractController
{
    private Security $security;

    private EntityManagerInterface $entityManager;

    private LogParser $parser;

    public function __construct(Security $security, EntityManagerInterface $entityManager, LogParser $parser)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
        $this->parser = $parser;
    }

    /**
     * @Route(name="api_login", methods = {"POST"})
     */
    public function index(): Response
    {
        /** @var User $user */
        $user = $this->security->getUser();
        if (null === $user) {
            return $this->json([
                'message' => 'missing credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $token = Uuid::v4();
        $user->setApiToken($token);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json([
            'user' => $user->getUserIdentifier(),
            'token' => $token,
        ]);
    }
}
