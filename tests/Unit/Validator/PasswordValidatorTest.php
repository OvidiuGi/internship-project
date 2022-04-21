<?php

namespace App\Tests\Unit\Validator;

use App\Validator\Cnp;
use App\Validator\Password;
use App\Validator\PasswordValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class PasswordValidatorTest extends ConstraintValidatorTestCase
{
    public function testCnpWithPassword(): void
    {
        self::expectException(UnexpectedTypeException::class);
        $this->validator->validate('5010911070069', new Cnp());
    }

//    public function testPasswordIsNull(): void
//    {
//        $this->validator->validate(null, new Password());
//        $this->buildViolation('Password not valid!')->assertRaised();
//    }

    /**
     * @dataProvider providePasswordValues
     */
    public function testPassword(string $password, bool $expected): void
    {
        $this->validator->validate($password, new Password());
        if ($expected) {
            $this->assertNoViolation();
        } else {
            $this->buildViolation('Password not valid!')->assertRaised();
        }
    }

    public function providePasswordValues(): array
    {
        return [
            ['', false, ['test']],
            ['parola', false, ['test']],
            ['parola!!!!!', false, ['test']],
            ['Parola11!@', true, ['test']],
            ['Parol a12!', false, ['test']],
        ];
    }

    protected function createValidator(): PasswordValidator
    {
        return new PasswordValidator();
    }
}
