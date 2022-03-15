<?php

namespace App\Tests\Controller\ArgumentValueResolver;

use App\Controller\ArgumentResolver\UserDtoArgumentValueResolver;
use App\Controller\Dto\UserDto;

use PHPUnit\Framework\TestCase;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class UserDtoArgumentValueResolverTest extends TestCase
{
    private UserDtoArgumentValueResolver $userDtoArgumentValueResolver;

    protected function setUp(): void
    {
        $this->userDtoArgumentValueResolver  = new UserDtoArgumentValueResolver();
    }

    public function testSupportsDtoClass(): void
    {
        $request = Request::create('/test');
        $argumentMetadata = new ArgumentMetadata('test',UserDto::class,true,true,true,true);
        $result = $this->userDtoArgumentValueResolver->supports($request,$argumentMetadata);

        self::assertNotFalse($result);
    }

    public function testResolve()
    {
        $request = Request::create(
            '/test',
            'GET',
            [],
            [],
            [],
            [],
            json_encode([
                'firstName' => 'Ovidiu',
                'lastName' => 'Gireada',
                'email' => 'example@chiwawa.wawa',
                'password' => 'MySecretPassword',
                'confirmPassword' => 'MySecretPassword',
                'cnp' =>'5010911070069'])
        );

        $dto = null;
        $argumentMetadata = new ArgumentMetadata('test',UserDto::class,true,true,true,true);

        foreach ($this->userDtoArgumentValueResolver->resolve($request,$argumentMetadata) as $result) {
            $dto = $result;
        }

        $userDto = new UserDto();
        $userDto->firstName = 'Ovidiu';
        $userDto->lastName = 'Gireada';
        $userDto->email = 'example@chiwawa.wawa';
        $userDto->password = 'MySecretPassword';
        $userDto->confirmPassword = 'MySecretPassword';
        $userDto->cnp = '5010911070069';

        self::assertEquals($userDto,$dto);
    }
}
