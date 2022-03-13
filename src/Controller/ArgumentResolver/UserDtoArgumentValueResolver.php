<?php

namespace App\Controller\ArgumentResolver;

use App\Controller\Dto\UserDto;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class UserDtoArgumentValueResolver implements ArgumentValueResolverInterface
{
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return $argument->getType() === UserDto::class;
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $data = $request->getContent();
        $decodedData = json_decode($data,true);
        $userDto = new UserDto();

        $userDto->cnp = $decodedData['cnp'];
        $userDto->firstName = $decodedData['firstName'];
        $userDto->lastName = $decodedData['lastName'];
        $userDto->email = $decodedData['email'];
        $userDto->password = $decodedData['password'];
        $userDto->confirmPassword = $decodedData['confirmPassword'];

        yield $userDto;
    }
}