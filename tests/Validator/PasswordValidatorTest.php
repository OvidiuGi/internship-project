<?php

namespace App\Tests\Validator;

use App\Validator\Password;
use App\Validator\PasswordValidator;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class PasswordValidatorTest extends ConstraintValidatorTestCase
{
    public function testPasswordIsNull(): void
    {
        $this->validator->validate(null, new Password());
//        var_dump($result);
        $this->buildViolation('Password not valid!')->assertRaised();
    }

    /**
     * @dataProvider providePasswordValues
     */
    public function testPassword(string $password, bool $expected): void
    {
        $result = $this->validator->validate($password, new Password());
        if($expected){
            $this->assertNoViolation();
        } else{
            $this->buildViolation('Password not valid!')->assertRaised();
        }
    }

    public function providePasswordValues()
    {
        return[
            ['', false, ['test']],
            ['parola', false, ['test']],
            ['parola!!!!!', false, ['test']],
            ['Parola11!@', true, ['test']],
            ['Parol a12!', false, ['test']],

        ];
    }

    protected function createValidator()
    {
        return new PasswordValidator();
    }
}