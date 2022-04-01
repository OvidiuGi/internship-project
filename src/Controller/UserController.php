<?php

namespace App\Controller;

use App\Controller\Dto\UserDto;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        SerializerInterface $serializer
    ) {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->passwordHasher = $passwordHasher;
        $this->userRepository = $userRepository;
        $this->serializer = $serializer;
    }

    /**
     * @Route(methods={"POST"})
     */
    public function register(UserDto $userDto): Response
    {
        $user = User::createFromDto($userDto);
        $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPlainPassword()));

        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            $errorArray = [];
            foreach ($errors as $error) {
                /*
                 * @var ConstraintViolation $error
                 */
                $errorArray[$error->getPropertyPath()] = $error->getMessage();
            }
            $this->logger->info('Failed registering a user.');

            return new JsonResponse($errorArray);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $this->entityManager->refresh($user);
        $savedDto = UserDto::createFromUser($user);

        $this->logger->info('An user is registered');

        return new JsonResponse($savedDto, Response::HTTP_CREATED);
    }

    /**
     * @Route(path="/delete/{email}", methods={"DELETE"})
     */
    public function softDelete(string $email)
    {
        $this->entityManager->getFilters()->enable('softdeleteable');

        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (null === $user) {
            $this->logger->info('Soft delete failed: no account for such email');

            return new JsonResponse('No account associated with email', Response::HTTP_NOT_FOUND, [], true);
        }
        $this->userRepository->remove($user);

        $this->logger->info('Account soft deleted: '.$email);

        return new JsonResponse('Account deleted.', Response::HTTP_FOUND, [], true);
    }

    /**
     * @Route(path="/recover/{email}", methods={"GET"})
     */
    public function softRecover(string $email)
    {
        $this->entityManager->getFilters()->disable('softdeleteable');
        $recoveredUser = $this->userRepository->findOneBy(['email' => $email]);

        if (null === $recoveredUser) {
            $this->logger->info('Soft delete recover failed: no account for such email');

            return new JsonResponse('No account associated with email', Response::HTTP_NOT_FOUND, [], true);
        }
        $recoveredUser->setDeletedAt(null);

        $this->entityManager->persist($recoveredUser);
        $this->entityManager->flush();

        $this->logger->info('Account recovered when soft deleted: '.$email);

        return new JsonResponse('Account restored.', Response::HTTP_FOUND, [], true);
    }

    /**
     * @Route(path="/show", methods={"GET"})
     */
    public function show(): Response
    {
        $users = $this->userRepository->findAll();

        $serializedData = $this->serializer->serialize(
            $users,
            'json'
        );

        return new JsonResponse($serializedData, Response::HTTP_OK, [], true);
    }
}
