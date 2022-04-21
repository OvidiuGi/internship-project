<?php

namespace App\Tests\Form\Type;

use App\Form\ForgotMyPasswordType;
use Symfony\Component\Form\Test\TypeTestCase;

class ForgotMyPasswordTypeTest extends TypeTestCase
{
    public function testSubmitValidData(): void
    {
        $formData = [
            'email' => 'email',
            'submit' => 'submit',
        ];
        $form = $this->factory->create(ForgotMyPasswordType::class);
        $resultedData = $form->submit($formData)->getData();
        $expected = ['email' => 'email'];

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($expected, $resultedData);
    }
}
