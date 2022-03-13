<?php

namespace App\Tests\Validator;

use App\Validator\Cnp;
use App\Validator\CnpValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class CnpValidatorTest extends ConstraintValidatorTestCase
{
    public function testCnpIsNull(): void
    {
        $this->validator->validate(null, new Cnp());
        $this->buildViolation('This is not a valid CNP')->assertRaised();
    }

    /**
     * @dataProvider provideCnpValues
     */
    public function testCnp(string $cnp, bool $expected): void
    {
        $this->validator->validate($cnp, new Cnp());
        if ($expected) {
            $this->assertNoViolation();
        } else {
            $this->buildViolation('This is not a valid CNP')->assertRaised();
        }
    }

    public function provideCnpValues(): array
    {
        return [
            ['5010911070069', true, ['test']],
            ['', false, ['test']],
            ['101', false, ['test']],
            ['50 ', false, ['test']],
            ['ups', false, ['test']],
            ['!_-@#$!%^', false, ['test']],
            ['5010911070669', false, ['test']],
            ['501091102!231', false, ['test']],
            ['501091107aa69', false, ['test']],
        ];
    }

    protected function createValidator(): CnpValidator
    {
        return new CnpValidator();
    }
}