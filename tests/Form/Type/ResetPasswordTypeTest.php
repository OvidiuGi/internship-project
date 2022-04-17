<?php

namespace App\Tests\Form\Type;

use App\Form\ResetPasswordType;
use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Test\TypeTestCase;

class ResetPasswordTypeTest extends TypeTestCase
{
    public function testSubmitValidData(): void
    {
        $formData = [
            'password' => 'password',
            'submit' => 'submit'
        ];
        $form = $this->factory->create(ResetPasswordType::class);
        $resultedData = $form->submit($formData)->getData();
        $expected = [];

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($expected, $resultedData);
    }
}
