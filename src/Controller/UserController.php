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
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route(path="/api/user")
 */
class UserController
{
    private ValidatorInterface $validator;
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
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

        $errors = $this->validator->validate($user);
        if(count($errors) > 0){
            $errorArray = [];
            foreach ($errors as $error) {
                /**
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

        return new JsonResponse($savedDto, Response::HTTP_CREATED);
    }
}