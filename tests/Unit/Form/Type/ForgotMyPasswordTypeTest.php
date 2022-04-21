<?php

namespace App\Tests\Unit\Form\Type;

use App\Form\ForgotMyPasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Test\TypeTestCase;

class ForgotMyPasswordTypeTest extends TypeTestCase
{
    private ForgotMyPasswordType $type;

    protected function setUp(): void
    {
        $this->type = new ForgotMyPasswordType();
    }

    public function testFormBuilder(): void
    {
        $formBuilder = $this->createMock(FormBuilderInterface::class);
        $formBuilder
            ->expects($this->exactly(2))
            ->method('add')
            ->withConsecutive(
                [
                    'email',
                    EmailType::class
                ],
                [
                    'submit',
                    SubmitType::class
                ]
            )
            ->willReturnSelf();

        $this->type->buildForm($formBuilder, []);
    }
}
