<?php

namespace App\Controller\ArgumentResolver;

use App\Controller\Dto\UserDto;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\SerializerInterface;

class UserDtoArgumentValueResolver implements ArgumentValueResolverInterface
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return UserDto::class === $argument->getType();
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $data = $request->getContent();

        yield $this->serializer->deserialize($data, UserDto::class, 'json');
    }
}
