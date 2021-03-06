<?php

namespace App\Controller\Api;

use App\Controller\Dto\UserDto;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route(path="/api/users")
 */
class UserController implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private LoggerInterface $analyticsLogger;

    private ValidatorInterface $validator;

    private EntityManagerInterface $entityManager;

    private UserPasswordHasherInterface $passwordHasher;

    private UserRepository $userRepository;

    private SerializerInterface $serializer;

    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $passwordHasher,
        UserRepository $userRepository,
        SerializerInterface $serializer,
        LoggerInterface $analyticsLogger
    ) {
        $this->entityManager = $entityManager;

        $this->validator = $validator;

        $this->passwordHasher = $passwordHasher;

        $this->userRepository = $userRepository;

        $this->serializer = $serializer;

        $this->analyticsLogger = $analyticsLogger;
    }

    /**
     * @Route(methods={"POST"},name="api_user_register")
     */
    public function register(UserDto $userDto): Response
    {
        $user = User::createFromDto($userDto);
        $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPlainPassword()));

        $errors = $this->validator->validate($user);
        if (\count($errors) > 0) {
            $errorArray = [];
            foreach ($errors as $error) {
                /*
                 * @var ConstraintViolation $error
                 */
                $errorArray[$error->getPropertyPath()] = $error->getMessage();
            }

            return new JsonResponse($errorArray);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $this->entityManager->refresh($user);
        $savedDto = UserDto::createFromUser($user);

        $this->analyticsLogger->info(
            'User successfully created',
            [
                'email' => $savedDto->email,
                'role' => $savedDto->roles[0],
                'type' => 'register',
                'firewall' => 'api',
                'success' => true,
            ]
        );

        return new JsonResponse($savedDto, Response::HTTP_CREATED);
    }

    /**
     * @Route(path="/{id}", methods={"DELETE"},name="api_user_delete")
     */
    public function softDelete(int $id): Response
    {
        $user = $this->userRepository->findOneBy(['id' => $id]);

        if (null === $user) {
            $this->logger->info('Soft delete failed: no account for such id');

            return new JsonResponse('No account associated with this id', Response::HTTP_NOT_FOUND, [], true);
        }

        $this->userRepository->remove($user);

        $this->logger->info('Account soft deleted', ['userId' => $id]);

        return new JsonResponse('Account deleted.', Response::HTTP_FOUND, [], true);
    }

    /**
     * @Route(methods={"PATCH"},name="api_user_recover")
     */
    public function recover(Request $request): Response
    {
        $email = $request->toArray()['email'];

        $this->entityManager->getFilters()->disable('softdeleteable');
        $userToBeRecovered = $this->userRepository->findOneBy(['email' => $email]);

        if (null === $userToBeRecovered) {
            $this->logger->info('Soft delete recover failed: no account for such email', ['email' => $email]);

            return new JsonResponse('No account associated with email', Response::HTTP_NOT_FOUND, [], true);
        }

        if (null === $userToBeRecovered->getDeletedAt()) {
            $this->logger->info('Recover failed: the account is active', ['userEmail' => $email]);

            return new JsonResponse('The account is already active!', Response::HTTP_OK, [], true);
        }
        $this->userRepository->recover($userToBeRecovered);

        $this->logger->info('Account recovered when soft deleted: ', ['userEmail' => $email]);

        return new JsonResponse('Account restored.', Response::HTTP_FOUND, [], true);
    }
}
