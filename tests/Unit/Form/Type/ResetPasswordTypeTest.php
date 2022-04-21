<?php

namespace App\Tests\Unit\Form\Type;

use App\Form\ResetPasswordType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Test\TypeTestCase;

class ResetPasswordTypeTest extends TypeTestCase
{
    private ResetPasswordType $type;

    protected function setUp(): void
    {
        $this->type = new ResetPasswordType();
    }

    public function testFormBuilder(): void
    {
        $formBuilder = $this->createMock(FormBuilderInterface::class);
        $formBuilder
            ->expects($this->exactly(2))
            ->method('add')
            ->withConsecutive(
                [
                    'password',
                    RepeatedType::class,
                    [
                        'type' => PasswordType::class,
                        'invalid_message' => 'The pasword fields must match.',
                        'required' => true,
                        'first_options' => ['label' => 'Password: '],
                        'second_options' => ['label' => 'Repeat Password: '],
                    ]
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
