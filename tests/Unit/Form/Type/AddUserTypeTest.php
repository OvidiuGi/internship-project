<?php

namespace App\Tests\Unit\Form\Type;

use App\Form\AddUserType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Test\TypeTestCase;

class AddUserTypeTest extends TypeTestCase
{
    private AddUserType $type;

    protected function setUp(): void
    {
        $this->type = new AddUserType();
    }

    public function testFormBuilder(): void
    {
        $formBuilder = $this->createMock(FormBuilderInterface::class);
        $formBuilder
            ->expects($this->exactly(8))
            ->method('add')
            ->withConsecutive(
                [
                    'firstname',
                    TextType::class,
                ],
                [
                    'lastname',
                    TextType::class,
                ],
                [
                    'email',
                    EmailType::class,
                ],
                [
                    'telephoneNr',
                    TextType::class,
                ],
                [
                    'plainPassword',
                    RepeatedType::class, [
                    'type' => PasswordType::class,
                    'invalid_message' => 'The pasword fields must match.',
                    'required' => true,
                    'first_options' => ['label' => 'Password: '],
                    'second_options' => ['label' => 'Repeat Password: ']
                    ]
                ],
                [
                    'cnp',
                    TextType::class,
                ],
                [
                    'role',
                    TextType::class,
                ],
                [
                    'submit',
                    SubmitType::class,
                ]
            )
            ->willReturnSelf();

        $this->type->buildForm($formBuilder, []);
    }
}
