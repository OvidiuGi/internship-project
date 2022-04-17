<?php

namespace App\Controller;

use Psr\Log\LoggerAwareTrait;
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

    use LoggerAwareTrait;

    private EntityManagerInterface $entityManager;

    public function __construct(Security $security, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/login", name="api_login", methods = {"POST"})
     */
    public function index(): Response
    {
        /** @var User $user */
        $user = $this->security->getUser();

        $this->logger->info('Started api login.');

        if (null === $user) {
            $this->logger->warning('User failed login, missing credentials');

            return $this->json([
                'message' => 'missing credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $token = Uuid::v4();
        $user->setApiToken($token);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->logger->info('Successful login of user', ['id' => $user->getId()]);

        return $this->json([
            'user' => $user->getUserIdentifier(),
            'token' => $token,
        ]);
    }
}
