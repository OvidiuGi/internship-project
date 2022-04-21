<?php

namespace App\Tests\Unit\Form\Type;

use App\Form\UpdateUserType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Test\TypeTestCase;

class UpdateUserTypeTest extends TypeTestCase
{
    private UpdateUserType $type;

    protected function setUp(): void
    {
        $this->type = new UpdateUserType();
    }

    public function testFormBuilder(): void
    {
        $formBuilder = $this->createMock(FormBuilderInterface::class);
        $formBuilder
            ->expects($this->exactly(5))
            ->method('add')
            ->withConsecutive(
                [
                    'firstName',
                    TextType::class,
                ],
                [
                    'lastName',
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
                    'submit',
                    SubmitType::class,
                ]
            )
            ->willReturnSelf();

        $this->type->buildForm($formBuilder, []);
    }
}
