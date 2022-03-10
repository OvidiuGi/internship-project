<?php

namespace App\Controller;

use App\Controller\Dto\UserDto;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Json;

/**
 * @Route(path="/api/user")
 */
class UserController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
//    /**
//     * @Route(methods={"POST"})
//     */
//    public function register_old(Request $request): Response
//    {
//        $data = $request->getContent();
//        $decodedData = json_decode($data,true);
//
//        $user = new User();
//        $user->cnp = $decodedData['cnp'];
//        $user->firstName = $decodedData['firstName'];
//        $user->lastName = $decodedData['lastName'];
//        $user->email = $decodedData['email'];
//        $user->password = $decodedData['password'];
//        $user->setRoles(['customer']);
//        $this->entityManager->persist($user);
//
//        $this->entityManager->flush();
//
//
//        return new JsonResponse($user, Response::HTTP_CREATED);
//    }

    /**
     * @Route(methods={"POST"})
     */
    public function register(UserDto $userDto): Response
    {

        $user = User::createFromDto($userDto);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $this->entityManager->refresh($user);
        $savedDto = UserDto::createFromUser($user);

        return new JsonResponse($savedDto, Response::HTTP_CREATED);
    }
}