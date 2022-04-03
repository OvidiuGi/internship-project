<?php

namespace App\Controller;

use App\Controller\Dto\UserDto;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
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

    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->passwordHasher = $passwordHasher;
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
}
