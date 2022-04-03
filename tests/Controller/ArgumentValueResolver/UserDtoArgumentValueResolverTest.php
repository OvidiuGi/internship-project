<?php

namespace App\Tests\Controller\ArgumentValueResolver;

use App\Controller\ArgumentResolver\UserDtoArgumentValueResolver;
use App\Controller\Dto\UserDto;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\SerializerInterface;

class UserDtoArgumentValueResolverTest extends TestCase
{
    private MockObject $serializer;

    private MockObject $request;

    private MockObject $argumentMetadata;

    private UserDtoArgumentValueResolver $userDtoArgumentValueResolver;

    public function setUp(): void
    {
        parent::setUp();

        $this->serializer = $this->createMock(SerializerInterface::class);

        $this->request = $this->createMock(Request::class);

        $this->argumentMetadata = $this->createMock(ArgumentMetadata::class);

        $this->userDtoArgumentValueResolver = new UserDtoArgumentValueResolver($this->serializer);
    }

    public function testSupportInvalidValue(): void
    {
        $this->argumentMetadata->expects($this->once())->method('getType')->willReturn('test');

        $this->assertFalse($this->userDtoArgumentValueResolver->supports($this->request, $this->argumentMetadata));
    }

    public function testSupportValidValue(): void
    {
        $this->argumentMetadata->expects($this->once())->method('getType')->willReturn(UserDto::class);

        $this->assertTrue($this->userDtoArgumentValueResolver->supports($this->request, $this->argumentMetadata));
    }
}
